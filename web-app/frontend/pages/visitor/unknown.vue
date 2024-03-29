<template>
  <div>
    <v-row justify="center">
      <v-dialog persistent v-model="previewImage" max-width="500px">
        <v-card>
          <v-card-title class="popup_background">
            <span class="headline" dense>Preview Image </span>
            <v-spacer></v-spacer>
            <v-icon dark @click="previewImage = false" outlined>
              mdi mdi-close-circle
            </v-icon>
          </v-card-title>
          <v-card-text>
            <p class="text-center">
              <v-img
                style="height: auto; text-align: center; width: auto"
                :src="previewImageSrc"
              >
              </v-img>
            </p>
          </v-card-text>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row>
      <v-col>
        <v-card class="mb-5" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> Un-Known Visitor - Logs</span></v-toolbar-title
            >
            <!-- <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              @click="getDataFromApi()"
              text
              title="Reload"
            >
              <v-icon class="ml-2" dark>mdi mdi-reload</v-icon>
            </v-btn>
            <!-- </template>
                  <span>Reload</span>
                </v-tooltip> -->

            <!-- <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }"> -->

            <span class="mt-8 ml-5">
              <v-menu
                width="80px!important"
                ref="from_menu_filter"
                v-model="from_menu_filter"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    width="80px!important"
                    :hide-details="!from_date"
                    outlined
                    dense
                    v-model="from_date"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                    placeholder="Select Date"
                  ></v-text-field>
                </template>
                <v-date-picker
                  style="height: 350px"
                  v-model="from_date"
                  no-title
                  scrollable
                  @input="
                    from_menu_filter = false;
                    getDataFromApi();
                  "
                >
                  <v-spacer></v-spacer>

                  <v-btn
                    text
                    color="primary"
                    @click="
                      from_menu_filter = false;
                      getDataFromApi();
                    "
                  >
                    Clear
                  </v-btn>
                </v-date-picker>
              </v-menu>
              <!-- <Calender
                style="width: 100%"
                @filter-attr="filterAttr"
                :defaultFilterType="1"
                :height="'28px '"
              /> -->
            </span>

            <v-spacer></v-spacer>
          </v-toolbar>

          <v-data-table
            dense
            :headers="headers"
            :items="data"
            model-value="data.id"
            :loading="loading"
            class="elevation-1"
          >
            <template v-slot:item.sno="{ item, index }">
              {{
                currentPage
                  ? (currentPage - 1) * perPage +
                    (cumulativeIndex + data.indexOf(item))
                  : "-"
              }}
            </template>
            <template v-slot:item.UserID="{ item }">
              <strong> {{ item.UserID ? item.UserID : "---" }}</strong>
            </template>
            <template v-slot:item.picture="{ item, index }">
              <v-row no-gutters>
                <v-col
                  style="
                    padding: 5px;
                    padding-left: 0px;
                    width: 100px;
                    max-width: 100px;
                  "
                >
                  <v-img
                    style="
                      border-radius: 10%;
                      height: auto;
                      width: 100px;
                      max-width: 100px;
                    "
                    :src="item.url"
                  >
                  </v-img>
                </v-col>
                <v-col style="padding: 10px"> </v-col>
              </v-row>
            </template>
            <template v-slot:item.device="{ item }">
              {{ item.device_name }}
            </template>
            <template v-slot:item.file="{ time }">
              {{ item.time }}
            </template>

            <template v-slot:item.preview="{ item }">
              <v-btn class="primary" @click="popupImage(item)"> Preview </v-btn>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  data: () => ({
    previewImage: false,
    previewImageSrc: "",
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    totalRowsCount: 0,
    purposeList: [],
    branchesList: [],
    id: "",
    from_menu_filter: "",
    from_date_filter: "",

    showFilters: false,
    filters: {},
    isFilter: false,
    generateLogsDialog: false,
    totalRowsCount: 0,
    //server_datatable_totalItems: 10,
    datatable_search_textbox: "",
    datatable_searchById: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    departments: [],
    Model: "Log",
    endpoint: "visitor_logs",

    from_date: null,
    from_menu: false,
    to_date: null,
    to_menu: false,

    payload: {},

    loading: true,

    date: null,
    menu: false,

    loading: false,
    time_menu: false,

    log_payload: {
      user_id: 41,
      device_id: "",
      date: null,
      time: null,
    },

    ids: [],

    data: [],
    devices: [],
    total: 0,
    pagination: {
      current: 1,
      total: 0,
      itemsPerPage: 1000,
    },
    payloadOptions: {},
    options: {
      current: 1,
      total: 0,
      itemsPerPage: 10,
    },
    errors: [],
    response: "",
    snackbar: false,
    headers: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "UserID",
        value: "sno",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Picture",
        align: "left",
        sortable: false,
        key: "picture", //sorting
        value: "picture", //edit purpose

        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Device Name",
        align: "left",
        sortable: false,
        key: "device", //sorting
        value: "device", //edit purpose

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Time",
        align: "left",
        sortable: false,
        key: "time", //sorting
        value: "time", //edit purpose

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Preview",
        align: "left",
        sortable: false,
        key: "preview", //sorting
        value: "preview", //edit purpose

        filterable: true,
        filterSpecial: false,
      },
    ],
    from_date: "",
    to_date: "",
  }),
  created() {
    let dt = new Date();
    let y = dt.getFullYear();
    let m = dt.getMonth() + 1;

    let dd = new Date(dt.getFullYear(), m, 0);

    m = m < 10 ? "0" + m : m;

    this.from_date = `${y}-${m}-${dt.getDate()}`;
    this.to_date = `${y}-${m}-${dt.getDate()}`;
    this.getDataFromApi();

    setInterval(() => {
      if (this.$route.name == "visitor-unknown") {
        this.getDataFromApi();
      }
    }, 1000 * 60);
  },

  methods: {
    filterAttr(data) {
      this.from_date = data.from;
      this.to_date = data.to;
      this.getDataFromApi();
    },
    popupImage(item) {
      this.previewImage = true;
      this.previewImageSrc = item.url;
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      url = "get-unknown-visitors-list";
      const { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";

      this.payloadOptions = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          from_date: this.from_date,
        },
      };
      if (filter_column != "")
        this.payloadOptions.params[filter_column] = filter_value;
      this.loading = true;
      this.$axios
        .get(`${url}?page=${this.options.page}`, this.payloadOptions)
        .then(({ data }) => {
          this.data = data;
          // this.total = data.length();
          this.loading = false;
          //this.totalRowsCount = data.length();
        });
    },
  },
};
</script>
