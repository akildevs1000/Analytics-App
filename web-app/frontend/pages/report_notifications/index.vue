<template>
  <div v-if="can('setting_company_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!preloader">
      <div class="text-center">
        <v-dialog persistent v-model="dialog" width="500">
          <v-card>
            <v-card-title dense class="popup_background">
              Send Test message to Whatsapp
              <v-spacer></v-spacer>
              <v-icon @click="dialog = false" outlined dark>
                mdi mdi-close-circle
              </v-icon>
            </v-card-title>
            <v-card-text class="mt-4">
              <v-text-field
                dense
                outlined
                placeholder="number"
                v-model="number"
              >
              </v-text-field>
              <v-textarea
                dense
                outlined
                placeholder="message"
                v-model="message"
              >
                Hello
              </v-textarea>
            </v-card-text>

            <!-- <v-divider></v-divider> -->

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn class="primary" @click="send" small dense> Send </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
      <!-- <Back class="primary white--text" /> -->

      <v-card class="mb-5 mt-2" elevation="0">
        <v-toolbar class="rounded-md" dense flat>
          <v-toolbar-title><span> Notifications List</span></v-toolbar-title>
          <!-- <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }"> -->
          <v-btn
            dense
            class="ma-0 px-0"
            x-small
            :ripple="false"
            text
            title="Reload"
          >
            <v-icon class="ml-2" @click="getDataFromApi()" dark
              >mdi mdi-reload</v-icon
            >
          </v-btn>
          <div v-if="isCompany" style="width: 250px">
            <v-select
              @change="getDataFromApi()"
              class="pt-10 px-2"
              v-model="branch_id"
              :items="[{ id: ``, branch_name: `Select All` }, ...branchesList]"
              dense
              placeholder="Select Branch"
              outlined
              item-value="id"
              item-text="branch_name"
            >
            </v-select>
          </div>
          <!-- </template>
            <span>Reload</span>
          </v-tooltip> -->

          <!-- <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }"> -->
          <!-- <v-btn
            x-small
            :ripple="false"
            text
            title="Filter"
            @click="toggleFilter()"
          >
            <v-icon dark>mdi-filter</v-icon>
          </v-btn> -->
          <!-- </template>
            <span>Filter</span>
          </v-tooltip> -->

          <v-spacer></v-spacer>

          <!-- <v-tooltip top color="primary">
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn x-small :ripple="false" text v-bind="attrs" v-on="on" @click="dialog = true">
                      <v-icon dark white>mdi mdi-whatsapp</v-icon>
                    </v-btn>
                  </template>
                  <span>Whatsapp Test</span>
                </v-tooltip> -->
          <!-- <v-tooltip top color="primary">
            <template v-slot:activator="{ on, attrs }"> -->
          <v-btn
            x-small
            :ripple="false"
            text
            title="Add   Notification"
            to="/report_notifications/create"
          >
            <v-icon dark>mdi-plus-circle-outline</v-icon>
          </v-btn>
          <!--</template>
            <span> Add Email Notification</span>
          </v-tooltip> -->
          <!-- <v-toolbar-items>
                  <v-col class="toolbaritems-button-design1">
                    <v-btn @click="dialog = true" small color="primary" class="primary mr-2 mb-2 toolbar-button-design1">
                      <v-icon small>mdi mdi-whatsapp</v-icon>
                    </v-btn>
                    <v-btn color="primary" small class="primary mr-2 mb-2 toolbar-button-design1"
                      to="/report_notifications/create">
                      <v-icon small>mdi mdi-email</v-icon>
                    </v-btn>
                  </v-col>
                </v-toolbar-items> -->
        </v-toolbar>

        <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
          </template>
        </v-snackbar>
        <v-data-table
          flat
          dense
          :headers="headers"
          :items="data"
          model-value="data.id"
          :loading="loading"
          :options.sync="options"
          :footer-props="{
            itemsPerPageOptions: [100, 500, 1000],
          }"
          hide-default-footer
          class="elevation-1"
          :server-items-length="totalRowsCount"
        >
          <template v-slot:header="{ props: { headers } }">
            <tr v-if="isFilter">
              <td v-for="header in headers" :key="header.text">
                <v-container>
                  <v-text-field
                    clearable
                    :hide-details="true"
                    v-if="header.filterable && !header.filterSpecial"
                    v-model="filters[header.value]"
                    :id="header.value"
                    @input="applyFilters(header.key, $event)"
                    outlined
                    dense
                    autocomplete="off"
                  ></v-text-field>
                </v-container>
              </td>
            </tr>
          </template>

          <template v-slot:item.medium="{ item }">
            <v-chip
              v-for="(medium, i) in item.mediums"
              :key="i"
              class="ma-1"
              small
              color="primary"
              >{{ medium }}</v-chip
            >
          </template>
          <template v-slot:item.reports="{ item }">
            <v-chip
              v-for="(report, i) in item.reports"
              :key="i"
              small
              color="primary"
              class="ma-1"
              >{{ report }}</v-chip
            >
          </template>
          <template v-slot:item.recipients="{ item }">
            <v-chip
              v-for="(to, i) in item.tos"
              :key="i"
              small
              color="primary"
              class="ma-1"
              :id="item.id"
              >{{ to }}</v-chip
            >
            <v-chip
              v-for="(cc, i) in item.ccs"
              :key="i"
              small
              color="primary"
              class="ma-1"
              :id="item.id"
              >{{ cc }} (Cc)</v-chip
            >

            <v-chip
              v-for="(bcc, i) in item.bccs"
              :key="i"
              small
              color="primary"
              class="ma-1"
              :id="item.id"
              >{{ bcc }} (Bcc)</v-chip
            >
          </template>
          <template v-slot:item.actions="{ item }">
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <div class="text-center">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </div>
              </template>
              <v-list width="120" dense>
                <v-list-item @click="editItem(item)">
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="secondary" small> mdi-pencil </v-icon>
                    Edit
                  </v-list-item-title>
                </v-list-item>
                <v-list-item @click="deleteItem(item)">
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="error" small> mdi-delete </v-icon>
                    Delete
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-card>
    </div>
    <Preloader v-else />
  </div>
  <NoAccess v-else />
</template>

<script>
import Back from "../../components/Snippets/Back.vue";

export default {
  components: { Back },

  data: () => ({
    showFilters: false,
    filters: {},
    isFilter: false,
    totalRowsCount: 0,
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

    dialog: false,
    color: "primary",
    endpoint: "report_notification",
    e1: 1,
    menu2: false,
    preloader: false,
    loading: false,
    response: false,
    id: "",
    snackbar: false,
    to: "",
    cc: "",
    bcc: "",
    number: "",
    message: "",
    payload: {
      report_types: [],
      mediums: [],
      frequency: null,
      time: null,
      tos: [],
      ccs: [],
      bccs: [],
    },
    data: [],
    options: {},
    errors: [],
    headers: [
      {
        text: "Subject",
        align: "left",
        sortable: true,
        key: "title",
        value: "subject",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Frequency",
        align: "left",
        sortable: true,
        key: "frequency",
        value: "frequency",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Time",
        align: "left",
        sortable: true,
        key: "time",
        value: "time",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Action",
        align: "left",
        sortable: false,
        key: "actions",
        value: "actions",
        filterable: false,
        filterSpecial: false,
      },
    ],
    branchesList: [],
    branch_id: "",
    isCompany: true,
  }),
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  async created() {
    this.preloader = false;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

    let branch_header = [
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "branch_id", //sorting
        value: "branch.branch_name", //edit purpose
        width: "300px",
        filterable: true,
        filterSpecial: true,
      },
    ];
    this.headers.splice(0, 0, ...branch_header);

    try {
      const { data } = await this.$axios.get(`branches_list`, {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      });
      this.branchesList = data;
    } catch (error) {
      // Handle the error
      console.error("Error fetching branch list", error);
    }

    this.id = this.$auth?.user?.company?.id;
    this.getDataFromApi();
  },
  methods: {
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    send() {
      // https://ezwhat.com/api/send.php?number=923108559858&type=text&message=test%20message&instance_id=64466B01B7926&access_token=a27e1f9ca2347bb766f332b8863ebe9f
      this.$axios
        .get(
          `https://ezwhat.com/api/send.php?number=${this.number}&type=text&message=${this.message}&instance_id=64466B01B7926&access_token=a27e1f9ca2347bb766f332b8863ebe9f`
        )
        .then(({ data }) => console.log(data));
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    editItem(item) {
      this.$router.push("/report_notifications/" + item.id);
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.response = data.message;
              this.getDataFromApi();
            }
          })
          .catch((err) => console.log(err));
    },

    add_to() {
      this.payload.tos.push(this.to);
      this.to = "";
    },
    add_cc() {
      this.payload.ccs.push(this.cc);
      this.cc = "";
    },
    add_bcc() {
      this.payload.bccs.push(this.bcc);
      this.bcc = "";
    },
    deleteTO(i) {
      this.payload.tos.splice(i, 1);
    },

    deleteCC(i) {
      this.payload.ccs.splice(i, 1);
    },

    deleteBCC(i) {
      this.payload.bccs.splice(i, 1);
    },
    applyFilters() {
      this.getDataFromApi();
    },
    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (
        (filter_column == "serach_medium" ||
          filter_column == "serach_email_recipients") &&
        filter_value != "" &&
        filter_value.length <= 5
      ) {
        this.snack = true;
        this.snackColor = "error";
        this.snackText = "Minimum 5 Characters to filter ";
        this.loading = false;
        return false;
      }
      this.loading = true;
      if (url == "") {
        url = this.endpoint;
      }
      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
          role_type: "employee",
          ...this.filters,
        },
      };

      if (filter_column != "") options.params[filter_column] = filter_value;

      this.$axios.get(url, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.data = data.data;
        this.totalRowsCount = data.total;
        this.total = data.total;
        this.loading = false;
      });
    },
  },
};
</script>
