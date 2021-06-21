<template>
    <div>
        <b-loading :is-full-page="isFullPage" v-model="isLoading" :can-cancel="false">
        </b-loading>

            <div class="columns mt-5">
                <div class="column ">
                    <b-field label="Заголовок">
                        <b-input
                            placeholder="Заголовок"
                            v-model="title"
                            range>
                        </b-input>
                    </b-field>
                </div>
            </div>

        <div class="columns ">

            <div class="column">
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
                                <option value="4">Автоматизация</option>
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
                        <b-button type="is-success is-light" v-on:click.prevent="sendDataRequest" expanded>Отправить
                        </b-button>
                    </div>

                </div>
            </div>

            <div class="column is-3">

                <div class="column mt-3">
                    <b-field label="Заказчик">
                        <b-select placeholder="Выберите отдел"
                                  v-model="customerGroup"
                        >
                            <option
                                v-for="option in menu"
                                :value="option.id"

                                :key="option.id">
                                {{ option.name }}
                            </option>
                        </b-select>
                    </b-field>
                </div>
                <div class="column ">
                    <b-field label="Согласование">
                        <b-taginput
                            v-model="checkboxGroup"
                            :data="filteredUsers"
                            autocomplete
                            ref="taginputOne"
                            icon="label"
                            placeholder="добавить"
                            @typing="getFilteredTags">
                            <template slot-scope="props">
                                <strong>{{props.option.id}}</strong>: {{props.option.f}} {{props.option.i}} {{props.option.o}}
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
                                    @close="$refs.taginputOne.removeTag(index, $event)">
                                    {{tag.f}}
                                </b-tag>
                            </template>
                        </b-taginput>
                    </b-field>
                </div>
                <div class="column">
                    <b-field label="Исполнители">
                        <b-taginput
                            v-model="users"
                            :data="filteredUsers"
                            autocomplete
                            ref="taginputTwo"
                            icon="label"
                            placeholder="добавить"
                            @typing="getFilteredTags">
                            <template slot-scope="props">
                                <strong>{{props.option.id}}</strong>: {{props.option.f}} {{props.option.i}} {{props.option.o}}
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
                                    @close="$refs.taginputTwo.removeTag(index, $event)">
                                    {{tag.f}}
                                </b-tag>
                            </template>
                        </b-taginput>
                    </b-field>
                </div>
                <div class="column ">
                    <b-field class="file">
                        <b-upload v-model="dropFiles" multiple class="file-label">
                            <span class="file-cta">
                                <b-icon class="file-icon" icon="upload"></b-icon>
                                <span class="file-label">Прикрепить файлы</span>
                            </span>
                        </b-upload>
                    </b-field>
                </div>
                <div class="column ">
                    <div class="tags">
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
            </div>
        </div>


    </div>

</template>

<style>
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
        data() {
            return {
                isLoading: false,
                isFullPage: true,
                form: new FormData(),//все данные по форме
                filteredUsers: this.people_job,
                filteredCustomer: this.menu,
                users: [],
                isActive: true,
                title: null,
                dates: [],
                task: null,
                priority: null,
                dropFiles: [],
                customerGroup: null,
                checkboxGroup: [],
                editor: DecoupledEditor,
                editorData: '',
                editorConfig: {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList',
                        'numberedList', 'blockQuote', '|', 'inserttable', 'tablecolumn', 'tablerow', 'mergetablecells', '|',
                        'underline', 'strikethrough', 'fontbackgroundcolor', 'fontcolor', 'fontsize', 'alignment:justify',
                        'alignment:center', 'alignment:right', 'alignment:left'
                    ],
                },
                menu: [],
                people_job: []
            }
        },
        methods: {

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
            formatDates(dates) {
                let date = new Date(dates)
                return date.toLocaleString('ru-RU')
            },
            sendDataRequest: async function () {

                   if(this.validateForm()){
                       this.warning()
                       return
                   }
                this.isLoading = true;

                //сохранение задачи
                this.form.append('title', this.title)
                this.form.append('date', this.dates)
                this.form.append('date_start', this.formatDates(this.dates[0]))
                this.form.append('date_end', this.formatDates(this.dates[1]))
                this.form.append('task', this.task)
                this.form.append('priority', this.priority)
                this.form.append('msg', this.editorData)

                //сохранение исполнителей
                this.form.append('performers', JSON.stringify(this.users))
                //сохранение заказчика
                this.form.append('customer', this.customerGroup)

                //сохранение подразделений
                this.form.append('reconciliations', JSON.stringify(this.checkboxGroup))

                //сохранение файлов
                this.form.append('files_count', this.dropFiles.length)
                this.dropFiles.forEach((item, index) => {
                    this.form.append('files[]', item)
                })

                let response = await axios.post(process.env.MIX_HTTP + window.location.hostname + '/save',
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
            loadSubdivisionsName: async function () {
                this.isLoading = true;

                let todos = [];

                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListSubdivisionsName')
                    .then((response) => todos = [...response.data.menus])
                    .catch(error => console.log(error))
                this.menu = []
                todos.forEach((value, index) => {
                    this.menu.push(value);
                });
                this.isLoading = false;
            },
            loadPeopleJob: async function () {
                this.isLoading = true;

                let todos = [];

                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListPeopleJob')
                    .then((response) => todos = [...response.data])
                    .catch(error => console.log(error))
                this.people_job = []
                todos.forEach((value, index) => {
                    this.people_job.push(value);
                });
                this.isLoading = false;
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
                this.filteredUsers = this.people_job.filter((option) => {
                    return option.f
                        .toString()
                        .toLowerCase()
                        .indexOf(text.toLowerCase()) >= 0
                })
            },
            getType(tag) {
                return 'is-success'
            },
            deleteDropFile(index) {
                this.dropFiles.splice(index, 1)
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
        mounted() {
            this.loadSubdivisionsName();
            this.loadPeopleJob();

        },
    }
</script>
