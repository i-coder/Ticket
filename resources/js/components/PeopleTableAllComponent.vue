<template>
    <div class="columns p-2">
        <b-loading :is-full-page="isFullPage" v-model="isLoading" :can-cancel="false">
        </b-loading>
        <div class="column is-3">

        </div>
        <div class="column is-9">
            <div class="field is-grouped is-grouped-multiline">
                <div class="control">
                    <div class="tags has-addons">
                        <span class="tag is-dark">Все</span>
                        <span class="tag is-warning">Исполнители</span>
                    </div>
                </div>
            </div>

            <b-table
                paginated
                backend-pagination
                :total="total"
                :per-page="perPage"
                @page-change="onPageChange"
                :data="data"
                :sort-icon="sortIcon"
                :sort-icon-size="sortIconSize"
                :pagination-rounded="true"
                :selected.sync="selected"
                :default-sort-direction="defaultSortOrder"
                :default-sort="[sortField, sortOrder]"
                aria-next-label="Next page"
                aria-previous-label="Previous page"
                aria-page-label="Page"
                aria-current-label="Current page">
                <template v-for="column in columns">
                    <b-table-column :key="column.id" v-bind="column">
                        <template
                            v-if="column.searchable"
                            #searchable="props">
                            <b-input
                                v-model="props.filters[props.column.field]"
                                placeholder="Search..."
                                icon="magnify"
                                size="is-small" />
                        </template>


                        <template v-slot="props">

                            <span v-if="props.column.field == 'id'">
                              {{props.row.id}}
                            </span>
                            <span v-if="props.column.field == 'f'">
                              <a :href="'/all/people/show?id=' + props.row.id">{{props.row.f}}</a>
                            </span>
                            <span v-if="props.column.field == 'i'">
                              {{props.row.i}}
                            </span>
                            <span v-if="props.column.field == 'o'">
                              {{props.row.o}}
                            </span>
                            <span v-if="props.column.field == 'performers'">
                              {{props.row.performers.length}}
                            </span>
                            <span v-if="props.column.field == 'reconciliations'">
                              {{props.row.reconciliations.length}}
                            </span>

                        </template>
                    </b-table-column>
                </template>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                sortIcon: 'arrow-up',
                sortIconSize: 'is-small',
                selectMenu: 1,
                isLoading: false,
                isFullPage: true,
                sortField: 'id',
                sortOrder: 'desc',
                defaultSortOrder: 'desc',
                page: 1,
                perPage: 15,
                total: 0,
                selected: null,
                data: [],
                columns: [
                    {
                        field: 'id',
                        label: 'ID',
                        width: '20',
                        numeric: true,
                        searchable: false,
                    },
                    {
                        field: 'f',
                        label: 'Фамилия',
                        width: '100',
                        searchable: true,
                    },
                    {
                        field: 'i',
                        label: 'Имя',
                        width: '100',
                        searchable: false,
                    }, {
                        field: 'o',
                        label: 'Отчество',
                        width: '100',
                        searchable: false,
                    },
                    {
                        field: 'performers',
                        label: 'Исполнение',
                        width: '100',
                        searchable: false,
                    },{
                        field: 'reconciliations',
                        label: 'Согласование',
                        width: '100',
                        searchable: false,
                    }
                ],
                draggingRow: null,
                draggingRowIndex: null,
                menu: [

                ],
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
                noStatus:'без статуса',

                activeUsers: [],
                messages: [],
                textMessage: '',
                isActive: false,
                typingTimer: false,
            }
        },
        methods: {
            zakazTextSearch(d) {
                var u = this.zakazText.find(item => item.id == d)
                if(!u){
                    return this.noStatus;
                }
                return u.name;
            },
            soglTextSearch(d) {
                var u = this.soglText.find(item => item.id == d)
                if(!u){
                    return this.noStatus;
                }
                return u.name;
            },
            isplTextSearch(d) {
                var u = this.isplText.find(item => item.id == d)
                if(!u){
                    return this.noStatus;
                }
                return u.name;
            },
            loadAsyncData: async function () {
                this.isLoading = true;
                const params = [
                    'api_key=bb6f51bef07465653c3e553d6ab161a8',
                    `sort_by=${this.sortField}.${this.sortOrder}`,
                    `page=${this.page}`,
                ].join('&')


                let todos = [];
                let allTotal = 0
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/all/people/work',
                    {
                        params: {
                            data: params
                        }
                    })
                    .then((response) => todos = [...response.data])
                    .catch(error => console.log(error))
                this.data = []
                todos.forEach((value, index) => {
                    this.data.push(value);
                    allTotal++;
                });
                this.total = allTotal
                this.isLoading = false;
            },
            onPageChange(page) {
                this.page = page
                this.loadAsyncData()
            },

        },
        mounted() {
            this.channel
                .here((users) => {
                    this.activeUsers = users;
                })
                .joining((user) => {
                    this.activeUsers.push(user);
                })
                .leaving((user) => {
                    this.activeUsers.splice(this.activeUsers.indexOf(user), 1);
                })
                .listen('TableAll', ({data}) => {
                    this.loadAsyncData();
                    this.$buefy.toast.open({
                        message: 'Обновление задач',
                        type: 'is-info',
                        position:'is-bottom-right',
                        duration:1000,

                    })
                    this.data.push({});
                    this.messages.push(data);
                    this.isActive = false;
                    console.log(data);
                })
                .listenForWhisper('typing', (e) => {
                    this.isActive = e;
                    if(this.typingTimer) clearTimeout(this.typingTimer);
                    this.typingTimer = setTimeout(() => {
                        this.isActive = false;
                    }, 2000);
                });

        },
        created() {
            this.loadAsyncData();
        },
        computed: {
            channel() {
                return window.Echo.join('room.refresh');
            }
        },
        watch: {
            'selectMenu': async function (bank) {
                this.loadAsyncData()
            },
        },
    }
</script>
