<template>
    <div>
        <b-loading :is-full-page="isFullPage" v-model="isLoading" :can-cancel="false">
        </b-loading>
        <div class="columns mt-5">
            <div class="column">
                <div class="columns">
                    <div class="column is-full">
                        <h1 class="title is-3">{{title}}</h1>
                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <b-field class="mb-0" label="Описание">
                            <p v-html="editorData"></p>
                        </b-field>

                    </div>
                </div>
                <div class="columns" v-if="arrZakazStatusArch.length>0">
                    <div class="column">
                        <b-field class="mb-0" label="История заказчика">
                        </b-field>
                        <div v-for="item in arrZakazStatusArch">
                            <span class="tag">{{item.user}}:</span>
                            <span class="tag">{{item.created_at}}</span>
                            <span class="">{{ zakazTextSearch(item.status)}}</span>
                        </div>

                    </div>
                </div>
                <div class="columns" v-if="user">
                    <div class="column">
                        <b-field class="mb-0" label="Задача создана">
                        </b-field>
                        <div>
                            <span class="tag">{{user.f}} {{user.i}} {{user.o}}</span>
                            <span class="tag">{{ticket.time}}</span>
                        </div>

                    </div>
                </div>
                <div class="columns" v-if="arrSoglStatusArch.length>0">
                    <div class="column">
                        <b-field class="mb-0" label="История согласования">
                        </b-field>
                        <div v-for="item in arrSoglStatusArch">
                            <span class="tag">{{item.user}}:</span>
                            <span class="tag">{{item.created_at}}</span>
                            <span class="">{{ soglTextSearch(item.status)}}</span>
                        </div>

                    </div>
                </div>

                <div class="columns" v-if="arrIsplProcentArch.length>0">
                    <div class="column">
                        <b-field class="mb-0" label="Процесс работы">
                        </b-field>
                        <div v-for="item in arrIsplProcentArch">
                            <span class="tag">{{item.user}}:</span>
                            <span class="tag">{{item.created_at}}</span>
                            <span class=""><b>{{ item.procent}}%</b></span>
                        </div>

                    </div>
                </div>
                <div class="columns" v-if="arrIsplStatusArch.length>0">
                    <div class="column">
                        <b-field class="mb-0" label="История исполнения">
                        </b-field>
                        <div v-for="item in arrIsplStatusArch">
                            <span class="tag">{{item.user}}:</span>
                            <span class="tag">{{item.created_at}}</span>
                            <span class="">{{ isplTextSearch(item.status)}}</span>

                        </div>

                    </div>
                </div>

                <div class="columns">
                    <div class="column">
                        <p v-for="item in comments" :key="item.id" class="mb-3" v-if="item.comment!='null'">
                                <b>№{{item.id}} </b> <b>{{formatDatesOne(item.date)}}</b>
                                <br> <a href="#" class="float-right">{{item.users}}</a><br>
                                <span>{{item.comment}}</span>
                                <br>
                                <span v-for="item2 in item.files" :key="item2.id">
                                    <span><a v-bind:href="'/uploads/ticket_attached_files/'+id+'/'+item2">{{item2}}</a> </span><br>
                                </span>
                        </p>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <b-field label="Комментарии">
                        </b-field>
                        <textarea class="textarea mb-1" rows="3" v-model="comment"
                                  placeholder="Текст сообщения..."></textarea>
                        <b-button type="is-success is-small rounded" @click="addComment"
                                  style="float: left;margin-right: 20px">добавить комментарий
                        </b-button>


                        <div class="tags mt-1">
                        <span v-for="(file, index) in dropFiles"
                              :key="index"
                              class="tag is-primary">
                            {{file.name}}
                            <button class="delete is-small"
                                    type="button"
                                    @click="deleteDropFile(index)">
                            </button>
                        </span>
                        </div>


                    </div>
                    <div class="column is-3 is-narrow">
                        <b-field label="Прикрепить файлы">
                        </b-field>
                        <b-field class="file">
                            <b-upload v-model="dropFiles" multiple drag-drop expanded style="height: 95px;">
                                <div class="content has-text-centered" style="padding-top: 15px;">
                                    <p>
                                        <b-icon icon="upload" size="is-large"></b-icon>
                                    </p>
                                </div>
                            </b-upload>
                        </b-field>
                    </div>
                </div>
            </div>


            <div class="column is-4">

                <div class="column">
                    <b-field class="mb-0" label="Заказчик">
                    </b-field>
                    <div>
                        <a :href="customer.id">{{customer.name}}</a>
                    </div>
                    <div v-if="editZakaz">
                        <div class="select is-small  mr-1" style="float: left">
                            <select v-model="statusZakaz">
                                <option value="1">Оценка работы 1</option>
                                <option value="2">Оценка работы 2</option>
                                <option value="3">Оценка работы 3</option>
                                <option value="4">Оценка работы 4</option>
                                <option value="5">Оценка работы 5</option>
                            </select>
                        </div>
                        <button class="button is-link is-small" v-on:click="addStatusZakaz">Записать</button>
                    </div>
                </div>
                <div class="column">
                    <b-field class="mb-0" label="Согласование">
                    </b-field>

                    <div v-for="user in checkboxGroup">
                        <div><a :href="user.id">{{user.fio}}</a></div>
                    </div>
                    <div v-if="editSogl">
                        <div class="select is-small  mr-1" style="float: left">
                            <select v-model="statusSogl">
                                <option value="1">не согласовано</option>
                                <option value="2">согласовано</option>
                            </select>
                        </div>
                        <button class="button is-link is-small" v-on:click="addStatusSogl">Записать</button>
                    </div>
                </div>
                <div class="column">
                    <b-field class="mb-0" label="Исполнители">
                    </b-field>

                    <div v-for="user in users" class="mb-2">
                        <div><a :href="user.id">{{user.fio}}</a></div>
                    </div>
                    <div v-if="editIspl">
                        <div class="select is-small mr-1" style="float: left">
                            <select v-model="statusIspl">
                                <option value="1">выполнено</option>
                                <option value="2">в работе</option>
                                <option value="3">не готово</option>
                                <option value="4">тестирование</option>
                                <option value="5">пауза</option>
                                <option value="6">отмена</option>
                            </select>
                        </div>
                        <button class="button is-link is-small" v-on:click="addStatusIspl">Записать</button>
                    </div>
                </div>
                <div class="column">
                    <b-field class="mb-0" label="Выполнено">
                        <div v-if="!editIspl"> - <b v-if="!statusProcent">0</b> <b v-if="statusProcent">{{statusProcent}}</b>%
                        </div>
                    </b-field>

                    <div v-if="editIspl">
                        <div class="select is-small mr-1" style="float: left">
                            <select v-model="selectProcentIspl">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="35">35</option>
                                <option value="40">40</option>
                                <option value="45">45</option>
                                <option value="50">50</option>
                                <option value="55">55</option>
                                <option value="60">60</option>
                                <option value="65">65</option>
                                <option value="70">70</option>
                                <option value="75">75</option>
                                <option value="80">80</option>
                                <option value="85">85</option>
                                <option value="90">90</option>
                                <option value="95">95</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <button class="button is-link is-small" v-on:click="addProcentIspl">Записать</button>
                    </div>
                </div>
                <div class="column" v-if="existingFiles.length>0">
                    <b-field class="mb-0" label="Файлы"></b-field>
                    <div class="tags">
                        <div v-for="(file, index) in existingFiles"
                             :key="index"
                             class="tag is-primary">
                            <a :href="file.path" class="link-in-tag">{{file.name}}</a>
                        </div>
                    </div>
                </div>


                <div class="column">
                    <b-field label="Статус согласования">
                        <div>- <b>{{statusTicket}}</b></div>
                    </b-field>
                </div>

                <div class="column">
                    <b-field label="Сроки">
                        С {{dates[0]}} по {{dates[1]}}
                    </b-field>
                </div>
                <div class="column">
                    <b-field
                        label="Тип задачи">
                        <div v-if="status == 0">Техническое задание</div>
                        <div v-if="status == 1">Согласование</div>
                        <div v-if="status == 2">Задача</div>
                        <div v-if="status == 3">Служебка</div>
                        <div v-if="status == 4">Автоматизация</div>
                    </b-field>
                </div>
                <div class="column">
                    <b-field
                        label="Приоритет">
                        <div v-if="priority == 1">Срочно</div>
                        <div v-if="priority == 2">Средний</div>
                        <div v-if="priority == 3">Текучка</div>
                    </b-field>

                </div>


            </div>
        </div>


    </div>
</template>

<style>

    .link-in-tag {
        color: #fff;
    }

    .link-in-tag:visited {
        color: white;
        background-color: transparent;
        text-decoration: none;
    }

    .link-in-tag:hover {
        color: white;
        background-color: transparent;
        text-decoration: underline;
    }

    .link-in-tag:active {
        color: wheat;
        background-color: transparent;
        text-decoration: underline;
    }

    .menu-list a.is-active {
        background-color: #aeaeae;
    }

    .document-editor {
        border: 1px solid var(--ck-color-base-border);
        border-radius: var(--ck-border-radius);

        /* Set vertical boundaries for the document editor. */
        max-height: 500px;

        /* This element is a flex container for easier rendering. */
        display: flex;
        flex-flow: column nowrap;
    }

    .document-editor__toolbar {
        /* Make sure the toolbar container is always above the editable. */
        z-index: 1;

        /* Create the illusion of the toolbar floating over the editable. */
        box-shadow: 0 0 5px hsla(0, 0%, 0%, .2);

        /* Use the CKEditor CSS variables to keep the UI consistent. */
        border-bottom: 1px solid var(--ck-color-toolbar-border);
    }

    /* Adjust the look of the toolbar inside the container. */
    .document-editor__toolbar .ck-toolbar {
        border: 0;
        border-radius: 0;
    }

    /* Make the editable container look like the inside of a native word processor application. */
    .document-editor__editable-container {

        padding: calc(2 * var(--ck-spacing-large));
        background: var(--ck-color-base-foreground);

        /* Make it possible to scroll the "page" of the edited content. */
        overflow-y: scroll;
    }

    .document-editor__editable-container .ck-editor__editable {
        /* Set the dimensions of the "page". */
        width: 15.8cm;
        min-height: 21cm;

        /* Keep the "page" off the boundaries of the container. */
        padding: 1cm 2cm 2cm;

        border: 1px hsl(0, 0%, 82.7%) solid;
        border-radius: var(--ck-border-radius);
        background: white;

        /* The "page" should cast a slight shadow (3D illusion). */
        box-shadow: 0 0 5px hsla(0, 0%, 0%, .1);

        /* Center the "page". */
        margin: 0 auto;
    }

    /* Set the default font for the "page" of the content. */
    .document-editor .ck-content,
    .document-editor .ck-heading-dropdown .ck-list .ck-button__label {
        font: 16px/1.6 "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    /* Adjust the headings dropdown to host some larger heading styles. */
    .document-editor .ck-heading-dropdown .ck-list .ck-button__label {
        line-height: calc(1.7 * var(--ck-line-height-base) * var(--ck-font-size-base));
        min-width: 6em;
    }

    /* Scale down all heading previews because they are way too big to be presented in the UI.
    Preserve the relative scale, though. */
    .document-editor .ck-heading-dropdown .ck-list .ck-button:not(.ck-heading_paragraph) .ck-button__label {
        transform: scale(0.8);
        transform-origin: left;
    }

    /* Set the styles for "Heading 1". */
    .document-editor .ck-content h2,
    .document-editor .ck-heading-dropdown .ck-heading_heading1 .ck-button__label {
        font-size: 2.18em;
        font-weight: normal;
    }

    .document-editor .ck-content h2 {
        line-height: 1.37em;
        padding-top: .342em;
        margin-bottom: .142em;
    }

    /* Set the styles for "Heading 2". */
    .document-editor .ck-content h3,
    .document-editor .ck-heading-dropdown .ck-heading_heading2 .ck-button__label {
        font-size: 1.75em;
        font-weight: normal;
        color: hsl(203, 100%, 50%);
    }

    .document-editor .ck-heading-dropdown .ck-heading_heading2.ck-on .ck-button__label {
        color: var(--ck-color-list-button-on-text);
    }

    /* Set the styles for "Heading 2". */
    .document-editor .ck-content h3 {
        line-height: 1.86em;
        padding-top: .171em;
        margin-bottom: .357em;
    }

    /* Set the styles for "Heading 3". */
    .document-editor .ck-content h4,
    .document-editor .ck-heading-dropdown .ck-heading_heading3 .ck-button__label {
        font-size: 1.31em;
        font-weight: bold;
    }

    .document-editor .ck-content h4 {
        line-height: 1.24em;
        padding-top: .286em;
        margin-bottom: .952em;
    }

    /* Set the styles for "Paragraph". */
    .document-editor .ck-content p {
        font-size: 1em;
        line-height: 1.63em;
        padding-top: .5em;
        margin-bottom: 1.13em;
    }

    /* Make the block quoted text serif with some additional spacing. */
    .document-editor .ck-content blockquote {
        font-family: Georgia, serif;
        margin-left: calc(2 * var(--ck-spacing-large));
        margin-right: calc(2 * var(--ck-spacing-large));
    }
</style>

<script>

    import DecoupledEditor from '@ckeditor/ckeditor5-build-decoupled-document';
    import Datepicker from 'vuejs-datepicker';

    let data = [
        {
            "id": 5,
            "first_name": "Погребняк",
            "last_name": "Роман"
        },
        {
            "id": 6,
            "first_name": "Sara",
            "last_name": "Armstrong"
        }
    ]

    export default {
        name: "TicketShowComponent",
        data() {
            return {
                arrIsplStatusArch: this.ispl,
                arrSoglStatusArch: this.sogl,
                arrZakazStatusArch: this.zakaz,
                arrIsplProcentArch: this.procentispl,

                editIspl: false,
                editSogl: false,
                editZakaz: false,

                selectProcentIspl: this.ticket.tekProcentIspl,
                statusProcent: this.ticket.tekProcentIspl,
                statusIspl: this.ticket.tekStatusIspl,
                statusSogl: this.ticket.tekStatusSogl,
                statusZakaz: this.ticket.tekStatusZakaz,

                statusIsplUser: this.ticket.isplUser,
                statusSoglUser: this.ticket.soglUser,

                tickets: this.ticket,
                auth: this.auth,
                comments: [],
                id: this.ticket.id,
                user: this.user,
                isLoading: false,
                isFullPage: true,
                form: new FormData(),//все данные по форме
                filteredUsers: data,
                users: this.performers,
                isActive: true,
                title: this.ticket.title,
                dates: [this.ticket.date_start, this.ticket.date_end],
                task: this.ticket.type_task,
                priority: this.ticket.priority,
                status: this.ticket.type_task,
                dropFiles: [],
                existingFiles: this.files,
                checkboxGroup: this.reconciliations,
                editor: DecoupledEditor,
                comment: null,
                editorData: this.ticket.msg,
                editorConfig: {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList',
                        'numberedList', 'blockQuote', '|', 'inserttable', 'tablecolumn', 'tablerow', 'mergetablecells', '|',
                        'underline', 'strikethrough', 'fontbackgroundcolor', 'fontcolor', 'fontsize', 'alignment:justify',
                        'alignment:center', 'alignment:right', 'alignment:left'
                    ],
                },
                soglText: [
                    {id: 1, name: 'Не согласован'},
                    {id: 2, name: 'Cогласован'},
                ],
                isplText: [
                    {id: 1, name: 'Выполнено'},
                    {id: 2, name: 'В работе'},
                    {id: 3, name: 'Не готово'},
                    {id: 4, name: 'Тестирование'},
                    {id: 5, name: 'Пауза'},
                    {id: 6, name: 'Отмена'},
                ],
                zakazText: [
                    {id: 1, name: 'Оценка работы 1'},
                    {id: 2, name: 'Оценка работы 2'},
                    {id: 3, name: 'Оценка работы 3'},
                    {id: 4, name: 'Оценка работы 4'},
                    {id: 5, name: 'Оценка работы 5'},
                ],
                menu: [],
                statusTicket: null,
            }
        },
        props: {
            user: {
                type: Array,
            },
            ticket: {
                type: Object,
            },
            performers: {
                type: Array,
            },
            reconciliations: {
                type: Array,
            },
            files: {
                type: Array,
            },
            comments: {
                type: Array,
            },
            ispl: {
                type: Object,
            },
            procentispl: {
                type: Object,
            },
            sogl: {
                type: Object,
            },
            zakaz: {
                type: Object,
            },
            customer: {
                type: Object,
            },
            auth: {
                type: Number,
            },
        },
        mounted() {
            this.authUser();
            this.realStatusTicket();
        },
        methods: {

            zakazTextSearch(d) {
                var u = this.zakazText.find(item => item.id == d)
                return u.name;
            },
            soglTextSearch(d) {
                var u = this.soglText.find(item => item.id == d)
                return u.name;
            },
            isplTextSearch(d) {
                var r = this.isplText.find(item => item.id == d)
                return r.name;
            },
            realStatusTicket: async function () {
                this.isLoading = true;

                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/real/status/ticket',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );

                this.isLoading = false;
                this.statusTicket = response.data

            },
            addStatusZakaz: async function () {
                this.isLoading = true;

                this.form.append('zakaz', this.statusZakaz)
                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/add/status/zakaz',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );

                this.form = new FormData()
                this.isLoading = false;
                location.reload();

            },
            addStatusIspl: async function () {
                this.isLoading = true;

                this.form.append('ispl', this.statusIspl)
                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/add/status/ispl',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );

                this.form = new FormData()
                this.isLoading = false;
                location.reload();

            },
            addStatusSogl: async function () {
                this.isLoading = true;

                this.form.append('sogl', this.statusSogl)
                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/add/status/sogl',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );

                this.form = new FormData()
                this.isLoading = false;
                location.reload();
            },
            addProcentIspl: async function () {
                this.isLoading = true;

                this.form.append('procent', this.selectProcentIspl)
                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/add/procent/ispl',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );

                this.form = new FormData()
                this.isLoading = false;
                location.reload();
            },
            authUser: async function () {
                this.isLoading = true;

                this.form.append('ispl', JSON.stringify(this.users))
                this.form.append('sogl', JSON.stringify(this.checkboxGroup))
                this.form.append('auth', this.auth)
                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/you/auth',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );
                if (response.data.sogl) {
                    this.editSogl = true
                }
                if (response.data.ispl) {
                    this.editIspl = true
                }
                if (response.data.zakaz) {
                    this.editZakaz = true
                }
                console.log(response.data)
                this.form = new FormData()
                this.isLoading = false;

            },
            addComment: async function () {

                if (!this.comment) {
                    this.warning()
                    return
                }

                this.isLoading = true;


                this.form.append('comment', this.comment)
                this.dropFiles.forEach((item, index) => {
                    this.form.append('files[]', item)
                })
                this.form.append('id', this.id)

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/addComment',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );
                console.log(response.data)
                response.data.files = JSON.parse(response.data.files)

                this.comments.push(response.data);
                this.isLoading = false;
                this.comment = null
                this.dropFiles = []
                this.form = new FormData()
            },
            formatDates(dates) {
                var formattedDates = [];
                formattedDates[0] = dates[0].toLocaleDateString(['ru-RU'], {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                formattedDates[1] = dates[1].toLocaleDateString(['ru-RU'], {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
                return formattedDates;
            },
            formatDatesOne(dates) {
                let date = new Date(dates)
                return date.toLocaleString('ru-RU')
            },
            validateForm() {
                if (this.users.length == 0) {
                    return true;
                }
                if (this.checkboxGroup.length == 0) {
                    return true;
                }
                if (this.editorData == null) {
                    return true;
                }
                if (this.task == null) {
                    return true;
                }
                if (this.priority == null) {
                    return true;
                }
                if (this.dates.length == 0) {
                    return true;
                }
                return true;
                /*return false;*/
            },
            sendDataRequest: async function () {

                /*   if(this.validateForm()){
                       this.warning()
                       return
                   }*/

                this.isLoading = true;

                //сохранение задачи
                this.form.append('id', this.id)
                this.form.append('title', this.title)
                this.form.append('date', this.dates)
                this.form.append('task', this.task)
                this.form.append('priority', this.priority)
                this.form.append('msg', this.editorData)

                //сохранение исполнителей
                this.form.append('performers', JSON.stringify(this.users))

                //сохранение подразделений
                this.form.append('reconciliations', JSON.stringify(this.checkboxGroup))

                //сохранение файлов
                this.form.append('files_count', this.dropFiles.length)
                this.dropFiles.forEach((item, index) => {
                    this.form.append('files[]', item)
                })

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/edit',
                    this.form,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'multipart/form-data; boundary=${form._boundary}'
                        },
                    }
                );

                this.isLoading = false;
                this.success();
            },
            success() {
                this.$buefy.toast.open({
                    message: 'Отправленно на согласование!'
                })
            },
            warning() {
                this.$buefy.toast.open({
                    message: 'Проблемы в форме (заполните все поля)',
                    type: 'is-danger',
                })
            },
            getFilteredTags(text) {
                this.filteredUsers = data.filter((option) => {
                    return option.first_name
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                })
            },
            getType(tag) {
                if (tag.id >= 1 && tag.id < 10) {
                    return 'is-primary'
                } else if (tag.id >= 10 && tag.id < 20) {
                    return 'is-danger'
                } else if (tag.id >= 20 && tag.id < 30) {
                    return 'is-warning'
                } else if (tag.id >= 30 && tag.id < 40) {
                    return 'is-success'
                } else if (tag.id >= 40 && tag.id < 50) {
                    return 'is-info'
                }
            },
            deleteDropFile(index) {
                this.dropFiles.splice(index, 1)
            },
            deleteExistingFile: async function (fileId, ticketId) {
                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/deleteExistingFile',
                    {
                        fileId: fileId,
                        ticketId: ticketId,
                    }
                ).then((response) => {
                    this.existingFiles = response.data;
                });
            },
            onReady(editor) {
                editor.ui.getEditableElement().parentElement.insertBefore(
                    editor.ui.view.toolbar.element,
                    editor.ui.getEditableElement(),
                );
                const toolbarContainer = document.querySelector('.document-editor__toolbar');
                toolbarContainer.appendChild(editor.ui.view.toolbar.element);
                window.editor = editor;
            }
        },
    }
</script>
