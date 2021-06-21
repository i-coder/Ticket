<template>
    <div class="columns p-2">
        <b-loading :is-full-page="isFullPage" v-model="isLoading" :can-cancel="false">
        </b-loading>
        <div class="column is-3">
            <div class="field is-grouped is-grouped-multiline">
                <div class="control">
                    <div class="tags has-addons">
                        <span class="tag is-dark">Фильтр</span>
                        <span class="tag is-danger">подразделений</span>
                    </div>
                </div>
            </div>
            <div class="" style="position: fixed ">
                <ul>
                    <li v-for="item in menu" :key="item.id" style="padding-left: 2px">

                        <a v-if="selectMenu==item.id" @click.prevent="selectMenu = item.id" class="tag" style="margin-left: 0px">
                            {{ item.name}}
                        </a>
                        <a v-if="selectMenu!=item.id" @click.prevent="selectMenu = item.id" class="tag"
                           style="">
                            {{ item.name}}
                        </a>
                        <small v-if="item.id==1" class="tag is-info is-light">{{countTicketAll}}</small>
                        <small class="tag is-info is-light" v-if="item.count_ticket!=0 && item.id>1">{{item.count_ticket}}</small>
                        <i v-if="selectMenu==item.id" style="color: #48c774" class=" fa fa-circle fa-fw"
                           aria-hidden="true"></i>
                    </li>
                </ul>
            </div>
        </div>
        <div class="column is-9">
            <div class="field is-grouped is-grouped-multiline">
                <div class="control">
                    <div class="tags has-addons">
                        <span class="tag is-dark">Все</span>
                        <span class="tag is-info">Исходящие</span>
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
                <b-table-column field="id" label="№" width="40" sortable numeric v-slot="props">
                    {{ props.row.id }}
                </b-table-column>
                <b-table-column field="title" label="Наименование" sortable v-slot="props">
                    {{ props.row.title }}
                </b-table-column>
                <b-table-column field="date_start" label="Начало" sortable v-slot="props">
                    {{ props.row.date_start }}
                </b-table-column>
                <b-table-column field="date_start" label="Конец" sortable v-slot="props">
                    {{ props.row.date_end }}
                </b-table-column>
                <b-table-column field="work_status" label="Раб статус" width="150" sortable v-slot="props">
                    <span class="tag is-success" v-if="props.row.work_status==1"> {{ isplTextSearch(props.row.work_status) }}</span>
                    <span class="tag is-warning" v-if="props.row.work_status==2"> {{ isplTextSearch(props.row.work_status) }}</span>
                    <span class="tag is-danger" v-if="props.row.work_status>2 || props.row.work_status==null"> {{ isplTextSearch(props.row.work_status) }}</span>
                </b-table-column>
                <b-table-column field="sogl_status" label="Сог статус" width="150" sortable v-slot="props">
                    <span class="tag is-success" v-if="props.row.sogl_status==2"> {{ soglTextSearch(props.row.sogl_status) }}</span>
                    <span class="tag is-danger" v-if="props.row.sogl_status!=2"> {{ soglTextSearch(props.row.sogl_status) }}</span>
                </b-table-column>
                <b-table-column field="action" label="Действие" width="40" sortable v-slot="props">
                    <a :href="'/show?id=' + props.row.id">Открыть</a>
                </b-table-column>
            </b-table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                countTicketAll:0,
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
                        width: '40',
                        numeric: true
                    },
                    {
                        field: 'title',
                        label: 'Название',
                    },


                    {
                        field: 'status',
                        label: 'Cтатус',
                        centered: true
                    },
                    {
                        field: 'actions',
                        label: 'Действия',
                        centered: true,
                    },
                ],
                draggingRow: null,
                draggingRowIndex: null,
                menu: [],
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
                ],
                zakazText: [
                    {id: 1, name: 'Оценка работы 1'},
                    {id: 2, name: 'Оценка работы 2'},
                    {id: 3, name: 'Оценка работы 3'},
                    {id: 4, name: 'Оценка работы 4'},
                    {id: 5, name: 'Оценка работы 5'},
                ],
                noStatus: 'без статуса'
            }
        },
        methods: {
            zakazTextSearch(d) {
                var u = this.zakazText.find(item => item.id == d)
                if (!u) {
                    return this.noStatus;
                }
                return u.name;
            },
            soglTextSearch(d) {
                var u = this.soglText.find(item => item.id == d)
                if (!u) {
                    return this.noStatus;
                }
                return u.name;
            },
            isplTextSearch(d) {
                var u = this.isplText.find(item => item.id == d)
                if (!u) {
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
                    `category=${this.selectMenu}`
                ].join('&')


                let todos = [];
                let allTotal = 0
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/outgoing/data',
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
            loadSubdivisionsName: async function () {
                this.isLoading = true;

                let todos = [];

                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListSubdivisionsNameCountAuthWork')
                    .then(
                        (response) => todos = [...response.data],
                    )
                    .catch(error => console.log(error))
                this.menu = []
                this.menu.push({'id': 1, 'name': 'Все подразделения'})

                todos.forEach((value, index) => {
                    this.menu.push(value);
                    if(value.count_ticket>0){
                        this.countTicketAll = value.count_ticket + this.countTicketAll;
                    }
                });
                this.isLoading = false;
                //this.loadSubdivisionsNameCount();
            },
            loadSubdivisionsNameCount: async function () {
                let todo = [];
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListSubdivisionsNameCount')
                    .then(
                        (response) => todo = [...response.data],
                    )
                    .catch(error => console.log(error))

                console.log(todo);
            },
        },
        mounted() {
            this.loadSubdivisionsName();

        },
        created() {
            this.loadAsyncData();
        },
        computed: {},
        watch: {
            'selectMenu': async function (bank) {
                this.loadAsyncData()
            },
        },
    }
</script>
