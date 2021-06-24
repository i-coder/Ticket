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
            <div class="" style="">
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
                        <small v-if="item.id==-1" class="tag is-info is-light">{{countTicketNoStatus}}</small>
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
                <b-table-column field="id" label="N" width="40" sortable numeric v-slot="props">
                    {{ props.row.id }}
                </b-table-column>
                <b-table-column field="title" label="Наименование" sortable v-slot="props">
                    <a :href="'/show?id=' + props.row.id">{{ props.row.title }}</a>
                </b-table-column>

                <b-table-column field="title" label="Исполнитель" sortable v-slot="props">
                    <div v-for="user in props.row.performers" class="mb-2">
                        <div>{{user.first_name}} {{user.last_name}}</div>
                    </div>
                </b-table-column>

                <b-table-column field="date_start" label="Начало" sortable v-slot="props">
                    {{ props.row.date_start }}
                </b-table-column>
                <b-table-column field="date_end" label="Конец" sortable v-slot="props">
                    {{ props.row.date_end }}
                </b-table-column>
                <b-table-column field="date_start" label="Тип" sortable v-slot="props">
                    <span class="tag is-light">{{ props.row.type_task }}</span>
                </b-table-column>
                <b-table-column field="date_start" label="Выполнено" sortable v-slot="props" width="100px">
                    <div style="width: 100px;border:1px solid #2a9055;background-color: white;text-align: center;">
                        <div class="" v-if="props.row.procent>0"
                             v-bind:style="{ 'width': (props.row.procent) + 'px'}"
                             style="background-color: #2a9055;text-align: center; color: black;position: relative;height: 20px">
                            <div style="position: absolute;left: 30%;">{{ props.row.procent }}%</div>
                        </div>
                        <div class="" v-if="props.row.procent==0"
                             v-bind:style="{ 'width': 100 + 'px'}"
                             style="color: red;height: 20px;position: relative;">
                            <div style="">{{ props.row.procent }}%</div>
                        </div>

                    </div>

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

            </b-table>

<!--            <b-table-->
<!--                paginated-->
<!--                backend-pagination-->
<!--                :total="total"-->
<!--                :per-page="perPage"-->
<!--                @page-change="onPageChange"-->
<!--                :data="data"-->
<!--                :pagination-rounded="true"-->
<!--                :striped="true"-->
<!--                aria-next-label="Next page"-->
<!--                aria-previous-label="Previous page"-->
<!--                aria-page-label="Page"-->
<!--                aria-current-label="Current page">-->
<!--                <template v-for="column in columns">-->
<!--                    <b-table-column :key="column.id" v-bind="column">-->
<!--                        <template-->
<!--                            v-if="column.searchable"-->
<!--                            #searchable="props">-->
<!--                            <b-input-->
<!--                                v-model="props.filters[props.column.field]"-->
<!--                                placeholder="Search..."-->
<!--                                icon="magnify"-->
<!--                                size="is-small" />-->
<!--                        </template>-->


<!--                        <template v-slot="props">-->

<!--                            <span v-if="props.column.field == 'id'" style="align-content: center">-->
<!--                                <b>{{props.row.id}}</b>-->
<!--                            </span>-->
<!--                            <span v-if="props.column.field == 'title'">-->
<!--                              <a :href="'/show?id=' + props.row.id">{{props.row.title}}</a>-->
<!--                            </span>-->

<!--                            <span v-if="props.column.field == 'performers'">-->
<!--                                 <div v-for="user in props.row.performers" class="mb-2">-->
<!--                                    <div>{{user.first_name}} {{user.last_name}}</div>-->
<!--                                </div>-->
<!--                            </span>-->
<!--                            <span v-if="props.column.field == 'date_start'">-->
<!--                              {{props.row.date_start}}-->
<!--                            </span>-->
<!--                            <span v-if="props.column.field == 'date_end'">-->
<!--                              {{props.row.date_end}}-->
<!--                            </span>-->
<!--                            <span v-if="props.column.field == 'type_task'" class="tag is-light">-->
<!--                              {{props.row.type_task}}-->
<!--                            </span>-->

<!--                            <span v-if="props.column.field == 'procent'">-->

<!--                                <div style="width: 100px;border:1px solid #2a9055;background-color: white;text-align: center;">-->
<!--                                    <div class="" v-if="props.row.procent>0"-->
<!--                                         v-bind:style="{ 'width': (props.row.procent) + 'px'}"-->
<!--                                         style="background-color: #2a9055;text-align: center; color: black;position: relative;height: 20px">-->
<!--                                        <div style="position: absolute;left: 30%;">{{ props.row.procent }}%</div>-->
<!--                                    </div>-->
<!--                                    <div class="" v-if="props.row.procent==0"-->
<!--                                         v-bind:style="{ 'width': 100 + 'px'}"-->
<!--                                         style="color: red;height: 20px;position: relative;">-->
<!--                                        <div style="">{{ props.row.procent }}%</div>-->
<!--                                    </div>-->
<!--                                </div>-->

<!--                            </span>-->

<!--                        </template>-->
<!--                    </b-table-column>-->
<!--                    -->
<!--                </template>-->
<!--            </b-table>-->
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
                draggingRow: null,
                draggingRowIndex: null,
                columns:[
                    {
                        field: 'id',
                        label: '№',
                        width: '15',
                        searchable: true,
                    },
                    {
                        field: 'title',
                        label: 'Наименование',
                        width: '200',
                        searchable: true,
                    },
                    {
                        field: 'performers',
                        label: 'Исполнитель',
                        width: '200',
                    },
                    {
                        field: 'date_start',
                        label: 'Начало',
                        width: '50',
                        searchable: true,
                    },
                    {
                        field: 'date_end',
                        label: 'Конец',
                        width: '50',
                        searchable: true,
                    },
                    {
                        field: 'type_task',
                        label: 'Тип',
                        width: '50',
                    },
                    {
                        field: 'procent',
                        label: 'Выполнено',
                        width: '50',
                    },
                    {
                        field: 'work_status',
                        label: 'Раб статус',
                        width: '50',
                    },
                    {
                        field: 'sogl_status',
                        label: 'Сог статус',
                        width: '50',
                    },
                ],
                menu: [

                ],
                soglText: [
                    {id: 1, name: 'не согласован'},
                    {id: 2, name: 'cогласован'},
                ],
                isplText: [
                    {id: 1, name: 'выполнено'},
                    {id: 2, name: 'в работе'},
                    {id: 3, name: 'не готово'},
                    {id: 4, name: 'тестирование'},
                    {id: 5, name: 'пауза'},
                ],
                zakazText: [
                    {id: 1, name: 'Оценка работы 1'},
                    {id: 2, name: 'Оценка работы 2'},
                    {id: 3, name: 'Оценка работы 3'},
                    {id: 4, name: 'Оценка работы 4'},
                    {id: 5, name: 'Оценка работы 5'},
                ],
                noStatus: 'без статуса',
                countTicketNoStatus:null
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
                await axios.get(process.env.MIX_HTTP + window.location.hostname + '/all',
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

                try {
                    const resp = await axios.get(process.env.MIX_HTTP + window.location.hostname + '/getListSubdivisionsName');
                    this.menu.push({'id': 1, 'name': 'Все подразделения'})
                    resp.data.menus.forEach((value, index) => {
                        this.menu.push(value);
                        this.countTicketAll = value.count_ticket + this.countTicketAll;
                    });
                    this.countTicketNoStatus = resp.data.noStatusTicket;
                    //this.menu.push({'id': -1, 'name': 'Без статусов'})
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
        },
        computed: {},
        watch: {
            'selectMenu': async function (bank) {
                this.loadAsyncData()
            },
        },
    }
</script>
