<template>
    <div>
        <div class="columns p-2">
            <b-loading :is-full-page="isFullPage" v-model="isLoading" :can-cancel="false">
            </b-loading>
            <div class="column is-3">

            </div>
            <div class="column is-9">
                <div class="field is-grouped is-grouped-multiline">
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag is-dark">Исполнение</span>
                            <span class="tag is-info">Задачи</span>
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
                        <span class="tag is-success" v-if="props.row.work_status==1 || props.row.work_status==2"> {{ isplTextSearch(props.row.work_status) }}</span>
                        <span class="tag is-danger" v-if="props.row.work_status>2 || props.row.work_status==null"> {{ isplTextSearch(props.row.work_status) }}</span>
                    </b-table-column>

                    <b-table-column field="action" label="Действие" width="40" sortable v-slot="props">
                        <a :href="'/show?id=' + props.row.id">Открыть</a>
                    </b-table-column>
                </b-table>
            </div>
        </div>

        <div class="columns p-2">
            <div class="column is-3">

            </div>
            <div class="column is-9">
                <div class="field is-grouped is-grouped-multiline">
                    <div class="control">
                        <div class="tags has-addons">
                            <span class="tag is-dark">Согласование</span>
                            <span class="tag is-info">Задачи</span>
                        </div>
                    </div>
                </div>
                <b-table
                    paginated
                    backend-pagination
                    :total="totalReconciliations"
                    :per-page="perPage"
                    @page-change="onPageChange"
                    :data="dataReconciliations"
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
    </div>

</template>

<script>
    export default {
        data() {
            return {
                countTicketAll: 0,
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
                dataReconciliations: [],
                totalReconciliations: 0,
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
                noStatus: 'без статуса',
                countTicketNoStatus: null
            }
        },
        props: {
            user_id: {
                type: Object,
            },

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
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/all/people/ticket',
                    {
                        params: {
                            data: params,
                            user_id: this.user_id
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
            loadAsyncDataReconciliations: async function () {
                this.isLoading = true;
                const params = [
                    'api_key=bb6f51bef07465653c3e553d6ab161a8',
                    `sort_by=${this.sortField}.${this.sortOrder}`,
                    `page=${this.page}`,
                    `category=${this.selectMenu}`
                ].join('&')

                let todos = [];
                let allTotal = 0
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/all/people/reconciliations',
                    {
                        params: {
                            data: params,
                            user_id: this.user_id
                        }
                    })
                    .then((response) => todos = [...response.data])
                    .catch(error => console.log(error))
                this.totalReconciliations = []
                todos.forEach((value, index) => {
                    this.dataReconciliations.push(value);
                    allTotal++;
                });
                this.totalReconciliations = allTotal
                this.isLoading = false;
            },
            onPageChange(page) {
                this.page = page
                this.loadAsyncData()
            },
            loadSubdivisionsName: async function () {
                this.isLoading = true;

                try {
                    const resp = await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListSubdivisionsName');
                    this.menu.push({'id': 1, 'name': 'Все подразделения'})
                    resp.data.menus.forEach((value, index) => {
                        this.menu.push(value);
                        this.countTicketAll = value.count_ticket + this.countTicketAll;
                    });
                    this.countTicketNoStatus = resp.data.noStatusTicket;
                    this.menu.push({'id': -1, 'name': 'Без статусов'})
                } catch (err) {
                    console.error(err);
                }
                this.isLoading = false;
                this.loadSubdivisionsNameCount();
            },
            loadSubdivisionsNameCount: async function () {
                let todo = [];
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListSubdivisionsName')
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
            this.loadAsyncDataReconciliations();
        },
        computed: {},
        watch: {
            'selectMenu': async function (bank) {
                this.loadAsyncData()
            },
        },
    }
</script>
