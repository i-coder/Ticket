<template>
    <div>
        <b-loading :is-full-page="isFullPage" v-model="isLoading" :can-cancel="false">
        </b-loading>
        <div class="columns mt-5">
            <div class="column">
                <div class="columns">
                    <div class="column is-half">
                        <b-field label="Статус">
                            <div v-if="status == 1">Согласован </div>
                            <div v-if="status == 0">Не согласован </div>
                            <b-field class="ml-2">
                                <b-checkbox v-model="status"
                                            true-value="1"
                                            false-value="0">
                                </b-checkbox>
                            </b-field>
                        </b-field>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-half">
                        <b-field label="Заголовок">
                            <b-input
                                placeholder="Заголовок"
                                v-model="title"
                                range>
                            </b-input>
                        </b-field>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <b-field label="Сроки">
                            <b-datepicker
                                placeholder="выбрать..."
                                v-model="dates"
                                range>
                            </b-datepicker>
                        </b-field>
                    </div>
                    <div class="column is-4">
                        <b-field
                            label="Тип задачи">
                            <b-select placeholder="Выбрать" expanded v-model="task">
                                <option value="0">Техническое задание</option>
                                <option value="1">Согласование</option>
                                <option value="2">Задача</option>
                                <option value="3">Служебка</option>
                            </b-select>
                        </b-field>
                    </div>
                    <div class="column is-3">
                        <b-field
                            label="Приоритет">
                            <b-select placeholder="Выбрать" expanded v-model="priority">
                                <option value="1">Срочно</option>
                                <option value="2">Средний</option>
                                <option value="3">Текучка</option>
                            </b-select>
                        </b-field>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <div class="document-editor">
                            <div class="document-editor__toolbar"></div>
                            <div class="document-editor__editable-container">
                                <div class="document-editor__editable">
                                    <ckeditor :editor="editor" @ready="onReady" v-model="editorData"
                                              :config="editorConfig"></ckeditor>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="columns">
                    <div class="column">
                        <b-button type="is-success is-light" v-on:click.prevent="sendDataRequest" expanded>Сохранить изменения</b-button>
                    </div>

                </div>
            </div>

            <div class="column">

                <div class="column"><b-menu>
                    <b-menu-list label="Согласование">
                        <b-menu-item icon="account" label="Подразделения">
                            <ul>
                                <li v-for="item in menu" :key="item.id">
                                    <b-checkbox  v-bind:native-value="item.id" type="is-success" v-model="checkboxGroup"> {{ item.name}}</b-checkbox>
                                </li>
                            </ul>
                        </b-menu-item>
                    </b-menu-list>
                </b-menu></div>
                <div class="column">
                    <b-field label="Исполнители">
                        <b-taginput
                            v-model="users"
                            :data="filteredUsers"
                            autocomplete
                            ref="taginput"
                            icon="label"
                            placeholder="добавить"
                            @typing="getFilteredTags">
                            <template slot-scope="props">
                                <strong>{{props.option.id}}</strong>: {{props.option.first_name}}
                            </template>
                            <template #empty>
                                не найден
                            </template>
                            <template #selected="props">
                                <b-tag
                                    v-for="(tag, index) in props.tags"
                                    :key="index"
                                    :type="getType(tag)"
                                    rounded
                                    :tabstop="false"
                                    ellipsis
                                    closable
                                    @close="$refs.taginput.removeTag(index, $event)">
                                    {{tag.first_name}}
                                </b-tag>
                            </template>
                        </b-taginput>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Файлы"></b-field>
                    <b-field class="file">
                        <b-upload v-model="dropFiles" multiple class="file-label">
                            <span class="file-cta">
                                <b-icon class="file-icon" icon="upload"></b-icon>
                                <span class="file-label">Прикрепить файлы</span>
                            </span>
                        </b-upload>
                    </b-field>
                </div>
                <div class="column">
                    <div class="tags">
                        <span v-for="(dropFile, dropFileIndex) in dropFiles"
                              :key="dropFileIndex"
                              class="tag is-primary">
                              {{dropFile.name}}
                            <button class="delete is-small"
                                    type="button"
                                    @click="deleteDropFile(index)">
                            </button>
                        </span>
                        <span v-for="(file, index) in existingFiles"
                              :key="index"
                              class="tag is-primary">
                            <a :href="file.path" class="link-in-tag">{{file.name}}</a>
                            <button class="delete is-small"
                                    type="button"
                                    @click="deleteExistingFile(file.id, ticket.id)">
                            </button>
                        </span>
                    </div>
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

.menu-list a.is-active{
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
    name: "TicketViewComponent",
    data() {
        return {
            id: this.ticket.id,
            isLoading: false,
            isFullPage: true,
            form: new FormData(),//все данные по форме
            filteredUsers: data,
            users: this.performers,
            isActive: true,
            title: this.ticket.title,
            dates: [new Date(this.ticket.date_start), new Date(this.ticket.date_end)],
            task:this.ticket.type_task,
            priority:this.ticket.priority,
            status: this.ticket.status,
            dropFiles: [],
            existingFiles: this.files,
            checkboxGroup: this.reconciliations,
            editor: DecoupledEditor,
            editorData: this.ticket.msg,
            editorConfig: {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList',
                    'numberedList', 'blockQuote', '|', 'inserttable', 'tablecolumn', 'tablerow', 'mergetablecells', '|',
                    'underline', 'strikethrough', 'fontbackgroundcolor', 'fontcolor', 'fontsize', 'alignment:justify',
                    'alignment:center', 'alignment:right', 'alignment:left'
                ],
            },
            menu:[
                {'name':'Все подразделения','id':'0'},
                {'name':'Корпоративный секретарь','id':'1'},
                {'name':'Служба комплаенс','id':'2'},
                {'name':'Служба внутренного аудита','id':'3'},
                {'name':'Комитет Совета Директоров по внутреннему аудиту','id':'4'},
                {'name':'Совет по управлению активами и пассивами','id':'5'},
                {'name':'Андеррайтинговый Совет','id':'6'},
                {'name':'Комитет Совета Директоров по социальным вопросам','id':'7'},
                {'name':'Комитет Совета Директоров по кадрам и вознаграждениям','id':'8'},
                {'name':'Управление бухгалтерского учета и отчетности','id':'9'},
                {'name':'Управление по управлению активами','id':'10'},
                {'name':'Направление андерайтинга','id':'11'},
                {'name':'Направление андерайтинга и перестрахования','id':'12'},
                {'name':'Департамент статистики','id':'13'},
                {'name':'Департамент продаж','id':'14'},
                {'name':'Департамент маркетинга и PR','id':'15'},
                {'name':'Колл-центр / Контакт центр','id':'16'},
                {'name':'Департамент регионального управления','id':'17'},
                {'name':'Отдел кадров / HR','id':'18'},
                {'name':'Юридическое управление','id':'19'},
                {'name':'Департамент страховых выплат','id':'20'},
                {'name':'Служба безопасности','id':'21'},
                {'name':'Департамент информационных технологий','id':'22'},
            ]
        }
    },
    props: {
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
    },
    methods: {
        validateForm(){
            if(this.users.length==0){
                return true;
            }
            if(this.checkboxGroup.length==0){
                return true;
            }
            if(this.editorData==null){
                return true;
            }
            if(this.task==null){
                return true;
            }
            if(this.priority==null){
                return true;
            }
            if(this.dates.length==0){
                return true;
            }return true;
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
            this.form.append('date',  this.dates)
            this.form.append('task', this.task)
            this.form.append('priority', this.priority)
            this.form.append('msg', this.editorData)

            //сохранение исполнителей
            this.form.append('performers', JSON.stringify(this.users))

            //сохранение подразделений
            this.form.append('reconciliations', JSON.stringify(this.checkboxGroup))

            //сохранение файлов
            this.form.append('files_count', this.dropFiles.length)
            this.dropFiles.forEach((item, index)=>{
                this.form.append('files[]', item)
            })

            let response = await axios.post(/*process.env.MIX_HTTP + window.location.hostname*/ 'http://ticket.test'+ '/edit',
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
