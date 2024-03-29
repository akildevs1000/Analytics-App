<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-dialog persistent v-model="DialogBox" width="900">
        <v-card>
          <v-toolbar class="popup_background" flat>
            {{ formAction }} Host

            <v-spacer></v-spacer>
            <span>
              <v-icon class="ml-2" @click="DialogBox = false" dark>
                mdi mdi-close-circle</v-icon
              >
            </span>
          </v-toolbar>
          <v-container>
            <v-row>
              <!-- <v-col cols="6">
                    <v-text-field
                      label="Company Name"
                      :disabled="disabled"
                      v-model="payload.company_name"
                      dense
                      menu-props="min-width: auto; max-height: 200px;"
                      class="text-center"
                      outlined
                      :hide-details="!errors.company_name"
                      :error="errors.company_name"
                      :error-messages="
                        errors && errors.company_name
                          ? errors.company_name[0]
                          : ''
                      "
                    ></v-text-field>
                  </v-col> -->

              <v-col cols="6">
                <v-autocomplete
                  label="Employee"
                  outlined
                  :disabled="disabled"
                  v-model="payload.employee_id"
                  :items="employees"
                  dense
                  item-text="full_name"
                  item-value="id"
                  :hide-details="!errors.employee_id"
                  :error-messages="
                    errors && errors.employee_id ? errors.employee_id[0] : ''
                  "
                >
                </v-autocomplete>
              </v-col>

              <v-col cols="6">
                <!-- <v-text-field
                  label="Zone. Number"
                  :disabled="disabled"
                  v-model="payload.zone_id"
                  dense
                  class="text-center"
                  outlined
                  :hide-details="!errors.zone_id"
                  :error="errors.zone_id"
                  :error-messages="
                    errors && errors.zone_id ? errors.zone_id[0] : ''
                  "
                ></v-text-field> -->
                <v-select
                  label="Zone"
                  :hide-details="true"
                  clearable
                  @change="applyFilters('status', $event)"
                  item-value="id"
                  item-text="name"
                  v-model="payload.zone_id"
                  outlined
                  dense
                  :items="zones_list"
                ></v-select>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="Flat Number"
                  :disabled="disabled"
                  v-model="payload.flat_number"
                  dense
                  menu-props="min-width: auto; max-height: 200px;"
                  class="text-center"
                  outlined
                  :hide-details="!errors.flat_number"
                  :error="errors.flat_number"
                  :error-messages="
                    errors && errors.flat_number ? errors.flat_number[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="Floor Number"
                  :disabled="disabled"
                  v-model="payload.floor_number"
                  dense
                  menu-props="min-width: auto; max-height: 200px;"
                  class="text-center"
                  outlined
                  item-text="name"
                  item-value="id"
                  :hide-details="!errors.floor_number"
                  :error="errors.floor_number"
                  :error-messages="
                    errors && errors.floor_number ? errors.floor_number[0] : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-text-field
                  label="Phone Number"
                  :disabled="disabled"
                  v-model="payload.number"
                  dense
                  menu-props="min-width: auto; max-height: 200px;"
                  class="text-center"
                  outlined
                  :hide-details="!errors.number"
                  :error="errors.number"
                  :error-messages="
                    errors && errors.number ? errors.number[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  label="Emergency Number"
                  :disabled="disabled"
                  v-model="payload.emergency_phone"
                  dense
                  menu-props="min-width: auto; max-height: 200px;"
                  class="text-center"
                  outlined
                  :hide-details="!errors.emergency_phone"
                  :error="errors.emergency_phone"
                  :error-messages="
                    errors && errors.emergency_phone
                      ? errors.emergency_phone[0]
                      : ''
                  "
                ></v-text-field>
              </v-col>

              <v-col cols="6">
                <v-menu
                  :disabled="disabled"
                  ref="openTimePicker"
                  v-model="openTimePicker"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      label="Office. Open time"
                      :disabled="disabled"
                      v-model="payload.open_time"
                      append-icon="mdi-clock-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      dense
                      class="text-center"
                      outlined
                      :hide-details="!errors.open_time"
                      :error="errors.open_time"
                      :error-messages="
                        errors && errors.open_time ? errors.open_time[0] : ''
                      "
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    format="ampm"
                    ampm-in-title
                    v-model="payload.open_time"
                    full-width
                    @input="$refs.openTimePicker.save(payload.open_time)"
                  ></v-time-picker>
                </v-menu>
              </v-col>

              <v-col cols="6">
                <v-menu
                  :disabled="disabled"
                  ref="closeTimePicker"
                  v-model="closeTimePicker"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      label="Office. Close time"
                      :disabled="disabled"
                      v-model="payload.close_time"
                      append-icon="mdi-clock-outline"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      dense
                      class="text-center"
                      outlined
                      :hide-details="!errors.close_time"
                      :error="errors.close_time"
                      :error-messages="
                        errors && errors.close_time ? errors.close_time[0] : ''
                      "
                    ></v-text-field>
                  </template>
                  <v-time-picker
                    format="ampm"
                    ampm-in-title
                    v-model="payload.close_time"
                    full-width
                    @input="$refs.closeTimePicker.save(payload.close_time)"
                  ></v-time-picker>
                </v-menu>
              </v-col>

              <v-col cols="6" v-if="disabled">
                <div class="px-5 popup_background">Host Link</div>
                <div>
                  <v-avatar class="ma-1" v-if="qrCodeDataURL" size="150" tile>
                    <img :src="qrCodeDataURL" alt="Avatar" />
                  </v-avatar>
                </div>
                <span>
                  <a :href="`${fullLink}`" target="_blank">
                    {{ fullLink }}
                  </a>
                </span>
              </v-col>

              <!-- <v-col cols="6" v-if="disabled">
                <div class="px-5 popup_background">Open Link</div>
                <div>
                  <v-avatar
                    class="ma-1"
                    v-if="qrCompanyCodeDataURL"
                    size="150"
                    tile
                  >
                    <img :src="qrCompanyCodeDataURL" alt="Avatar" />
                  </v-avatar>
                </div>
                <span>
                  <a :href="`${fullCompanyLink}`" target="_blank">
                    {{ fullCompanyLink }}
                  </a>
                </span>
              </v-col> -->
              <!-- <v-col cols="12">
                    <v-switch
                      :disabled="disabled"
                      dense
                      v-model="payload.weekend"
                      :label="`Weekend`"
                      :hide-details="!errors.weekend"
                      :error="errors.weekend"
                      :error-messages="
                        errors && errors.weekend ? errors.weekend[0] : ''
                      "
                    ></v-switch>
                  </v-col> -->
              <!-- <v-col cols="12">
                    <v-switch
                      :disabled="disabled"
                      dense
                      v-model="payload.webaccess"
                      :label="`Web Access`"
                      :hide-details="!errors.webaccess"
                      :error="errors.webaccess"
                      :error-messages="
                        errors && errors.webaccess ? errors.webaccess[0] : ''
                      "
                    ></v-switch>
                  </v-col> -->
            </v-row>
          </v-container>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <div class="text-right">
              <v-btn small color="grey white--text" @click="DialogBox = false">
                Close
              </v-btn>

              <v-btn
                v-if="can('employee_create') && formAction == 'Create'"
                small
                :loading="loading"
                color="primary"
                @click="submit"
              >
                Submit
              </v-btn>
              <v-btn
                v-else-if="can('employee_create') && formAction == 'Edit'"
                small
                :loading="loading"
                color="primary"
                @click="update_data"
              >
                Update
              </v-btn>
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
      <div v-if="can(`employee_view`)">
        <v-container>
          <v-card elevation="0">
            <v-toolbar class="mb-2" dense flat>
              <v-toolbar-title
                ><span>{{ Model }}s </span></v-toolbar-title
              >
              <span>
                <v-btn
                  dense
                  class="ma-0 px-0"
                  x-small
                  :ripple="false"
                  text
                  title="Reload"
                >
                  <v-icon class="ml-2" @click="clearFilters" dark
                    >mdi mdi-reload</v-icon
                  >
                </v-btn>
              </span>
              <span>
                <v-btn
                  dense
                  class="ma-0 px-0"
                  x-small
                  :ripple="false"
                  text
                  title="Filter"
                >
                  <v-icon class="ml-2" @click="toggleFilter" dark
                    >mdi mdi-filter</v-icon
                  >
                </v-btn>
              </span>
              <v-spacer></v-spacer>
            </v-toolbar>
            <v-data-table
              dense
              :headers="headers"
              :items="data"
              model-value="data.id"
              :loading="loadinglinear"
              :options.sync="options"
              :footer-props="{
                itemsPerPageOptions: [100, 500, 1000],
              }"
              class="elevation-1"
              :server-items-length="totalRowsCount"
            >
              <template v-slot:header="{ props: { headers } }">
                <tr v-if="isFilter">
                  <td v-for="header in headers" :key="header.text">
                    <v-container>
                      <v-text-field
                        clearable
                        @click:clear="
                          filters[header.value] = '';
                          applyFilters();
                        "
                        :hide-details="true"
                        v-if="header.filterable && !header.filterSpecial"
                        v-model="filters[header.value]"
                        :id="header.value"
                        @input="applyFilters(header.key, $event)"
                        outlined
                        dense
                        autocomplete="off"
                      ></v-text-field>
                      <v-select
                        v-if="
                          header.filterSpecial && header.value == 'branch_id'
                        "
                        :hide-details="true"
                        clearable
                        @change="applyFilters('status', $event)"
                        item-value="id"
                        item-text="branch_name"
                        v-model="filters[header.value]"
                        outlined
                        dense
                        :items="[
                          { branch_name: `All Branches`, id: `` },
                          ...branchesList,
                        ]"
                      ></v-select>
                      <v-select
                        v-if="header.filterSpecial && header.value == 'zone_id'"
                        :hide-details="true"
                        clearable
                        @change="applyFilters('status', $event)"
                        item-value="id"
                        item-text="name"
                        v-model="filters[header.value]"
                        outlined
                        dense
                        :items="[{ name: `All Zones`, id: `` }, ...zones_list]"
                      ></v-select>
                    </v-container>
                  </td>
                </tr>
              </template>
              <template v-slot:item.sno="{ item, index }">
                {{
                  currentPage
                    ? (currentPage - 1) * perPage +
                      (cumulativeIndex + data.indexOf(item))
                    : "-"
                }}
              </template>
              <template
                v-slot:item.first_name="{ item, index }"
                style="width: 300px"
              >
                <v-row no-gutters>
                  <v-col
                    style="
                      padding: 5px;
                      padding-left: 0px;
                      width: 50px;
                      max-width: 50px;
                    "
                  >
                    <v-img
                      style="
                        border: 1px solid #ddd;
                        border-radius: 50%;
                        height: auto;
                        width: 50px;
                        max-width: 50px;
                        height: 50px;
                      "
                      :src="
                        item.employee
                          ? item.employee.profile_picture
                          : '/no-profile-image.jpg'
                      "
                    >
                    </v-img>
                  </v-col>
                  <v-col style="padding: 10px">
                    <strong>
                      {{
                        item.employee
                          ? item.employee.first_name +
                            " " +
                            item.employee.last_name +
                            "-" +
                            item.employee.employee_id
                          : "---"
                      }}
                    </strong>
                    <!-- <div>
                      {{ item.employee ? item.employee.user.email : "---" }}
                    </div> -->
                  </v-col>
                </v-row>
              </template>

              <template v-slot:item.branch_id="{ item }">
                {{ item?.branch?.branch_name }}
              </template>
              <template v-slot:item.zone_id="{ item }">
                {{ item?.zone?.name }}
              </template>

              <template v-slot:item.options="{ item }">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                  </template>
                  <v-list width="120" dense>
                    <v-list-item @click="viewItem(item)">
                      <v-list-item-title style="cursor: pointer">
                        <v-icon color="secondary" small> mdi-eye </v-icon>
                        View
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </template>
            </v-data-table>
          </v-card>
        </v-container>
      </div>
    </div>
    <Preloader v-else />
  </div>
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  components: {
    VueCropper,
  },

  data: () => ({
    zones_list: [],
    originalURL: process.env.APP_URL + "/register/visitor/", // `https://mytime2cloud.com/register/visitor/`,
    fullCompanyLink: ``,
    encryptedID: "",
    fullLink: "",
    qrCodeDataURL: "",
    qrCompanyCodeDataURL: "",
    disabled: false,
    openTimePicker: false,
    closeTimePicker: false,

    menu: false,
    payload: {
      flat_number: "",
      floor_number: "",
      manager_name: "",
      number: "",
      emergency_phone: "",
      email: "",
      open_time: "",
      close_time: "",
      zone_id: 1,
      weekend: 1,
      webaccess: 1,
    },

    tab: null,

    totalRowsCount: 0,
    filters: {},
    isFilter: false,
    sortBy: "id",
    sortDesc: false,
    snack: false,
    snackColor: "",
    snackText: "",
    loadinglinear: true,
    displayErrormsg: false,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    dialogCropping: false,
    tabMenu: [],
    tab: "0",
    employeeId: 0,
    employeeObject: {},
    attrs: [],
    dialog: false,
    editDialog: false,
    viewDialog: false,
    selectedFile: "",
    DialogBox: false,
    m: false,
    expand: false,
    expand2: false,
    boilerplate: false,
    right: true,
    rightDrawer: false,
    drawer: true,
    tab: null,
    selectedItem: 1,
    on: "",
    files: "",
    search: "",
    loading: false,
    //total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 1000,
    ListName: "",
    color: "background",
    response: "",
    snackbar: false,
    btnLoader: false,
    max_employee: 0,
    employee: {
      title: "Mr",
      display_name: "",
      employee_id: "",
      system_user_id: "",
    },
    upload: {
      name: "",
    },
    previewImage: null,

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Host",
    endpoint: "host",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    designations: [],
    roles: [],
    employees: [],
    department_filter_id: "",
    dialogVisible: false,
    payloadOptions: {},
    // "": "03:50:00",
    // "": "08:50:00",
    // "zone_id": 1,
    // "weekend": true,
    // "webaccess": true,
    headers: [
      {
        text: "#",
        align: "left",
        sortable: true,
        key: "sno",
        value: "sno",

        filterable: false,
        filterSpecial: false,
      },
      {
        text: "Host Name",
        align: "left",
        sortable: true,
        key: "first_name",
        value: "first_name",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Zone",
        align: "left",
        sortable: true,
        key: "zone_id",
        value: "zone_id",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Flat No",
        align: "left",
        sortable: true,
        key: "flat_number",
        value: "flat_number",
        filterable: true,

        filterSpecial: false,
      },
      // {
      //   text: "Floor No",
      //   align: "left",
      //   sortable: true,
      //   key: "floor_number",
      //   value: "floor_number",
      //   filterable: true,
      //   width: "150px",
      //   filterSpecial: false,
      // },
      {
        text: "Phone",
        align: "left",
        sortable: true,
        key: "number",
        value: "number",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Emergency Phone",
        align: "left",
        sortable: true,
        key: "emergency_phone",
        value: "emergency_phone",

        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Office Openning Time",
        align: "left",
        sortable: true,
        key: "open_time",
        value: "open_time",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Close Openning Time",
        align: "left",
        sortable: true,
        key: "close_time",
        value: "close_time",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Options",
        align: "left",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
    formAction: "Create",
    branchesList: [],

    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    totalRowsCount: 0,
  }),

  async created() {
    this.originalURL = process.env.APP_URL + "/register/visitor/";
    if (this.$auth.user.branch_id == null || this.$auth.user.branch_id == 0) {
      let branch_header = [
        {
          text: "Branch",
          align: "left",
          sortable: true,
          value: "branch_id",
          filterable: true,
          filterName: "branch_id",
          filterSpecial: true,
        },
      ];
      this.headers.splice(1, 0, ...branch_header);
    }
    this.loading = false;
    this.boilerplate = true;

    this.payloadOptions = {
      params: {
        per_page: 10,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDataFromApi();
    await this.initialize();
  },

  mounted() {},
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,

          // branch_id: this.$auth.user.branch_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;
      });
    },
    getZonesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,

          // branch_id: this.$auth.user.branch_id,
        },
      };

      this.$axios.get(`zone_list`, this.payloadOptions).then(({ data }) => {
        this.zones_list = data;
      });
    },

    async initialize() {
      try {
        const options = {
          params: { per_page: 1000, company_id: this.$auth.user.company_id },
        };

        const { data } = await this.$axios.get(`employee`, options);

        this.employees = data.data.map((e) => ({
          id: e.id,
          first_name: e.first_name,
          last_name: e.last_name,
          display_name: e.display_name,
          branch_id: e.branch_id,
          full_name: e.full_name,
        }));
      } catch (error) {
        console.error("An error occurred:", error);
      }
    },
    encrypt() {
      this.encryptedID = this.$crypto.encrypt(id);
      // this.fullLink = this.originalURL + this.encryptedID;
    },
    closeViewDialog() {
      this.viewDialog = false;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    closePopup() {
      //croppingimagestep5
      this.$refs.attachment_input.value = null;
      this.dialogCropping = false;
    },
    saveCroppedImageStep2() {
      this.cropedImage = this.$refs.cropper.getCroppedCanvas().toDataURL();

      this.image_name = this.cropedImage;
      this.previewImage = this.cropedImage;

      this.dialogCropping = false;
    },
    close() {
      this.dialog = false;
      this.errors = [];
      setTimeout(() => {}, 300);
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    onPageChange() {
      this.getDataFromApi();
    },
    applyFilters() {
      this.getDataFromApi();
    },
    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
      if (this.isFilter) {
        this.getbranchesList();
        this.getZonesList();
      }
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint) {
      //this.loading = true;
      this.loadinglinear = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };
      this.currentPage = page;
      this.perPage = itemsPerPage;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.data = data.data;
        //this.server_datatable_totalItems = data.total;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;

        this.totalRowsCount = data.total;

        this.data.length == 0
          ? (this.displayErrormsg = true)
          : (this.displayErrormsg = false);

        this.loadinglinear = false;
      });
    },
    addItem() {
      this.getZonesList();
      this.disabled = false;
      this.formAction = "Create";
      this.DialogBox = true;
      this.payload = {};
    },
    editItem(item) {
      this.getZonesList();
      this.disabled = false;
      this.formAction = "Edit";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
    },
    viewItem(item) {
      this.getZonesList();
      this.disabled = true;
      this.formAction = "View";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
      this.fullLink =
        this.originalURL + this.$auth.user.company_id + "-" + item.id;
      this.fullCompanyLink = this.originalURL + this.$auth.user.company_id;
      this.generateQRCode(this.fullLink);
      this.generateCompanyQRCode(this.fullCompanyLink);
    },
    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`${this.endpoint}/${item.id}`)
          .then(({ data }) => {
            this.getDataFromApi();
            this.snackbar = true;
            this.response = "Record deleted successfully";
          })
          .catch((err) => console.log(err));
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    onpick_attachment() {
      this.$refs.attachment_input.click();
    },
    attachment(e) {
      this.upload.name = e.target.files[0] || "";

      let input = this.$refs.attachment_input;
      let file = input.files;

      if (file[0].size > 1024 * 1024) {
        e.preventDefault();
        this.errors["profile_picture"] = [
          "File too big (> 1MB). Upload less than 1MB",
        ];
        return;
      }

      if (file && file[0]) {
        let reader = new FileReader();
        reader.onload = (e) => {
          //croppedimage step6
          // this.previewImage = e.target.result;

          this.selectedFile = event.target.result;

          this.$refs.cropper.replace(this.selectedFile);
        };
        reader.readAsDataURL(file[0]);
        this.$emit("input", file[0]);

        this.dialogCropping = true;
      }
    },
    mapper(obj) {
      let formData = new FormData();

      for (let x in obj) {
        formData.append(x, obj[x]);
      }
      if (this.upload) {
        formData.append("logo", this.upload.name);
      }

      formData.append("company_id", this.$auth.user.company_id);

      return formData;
    },

    async generateQRCode(fullLink) {
      try {
        this.qrCodeDataURL = await this.$qrcode.generate(fullLink);
      } catch (error) {
        console.error("Error generating QR code:", error);
      }
    },

    async generateCompanyQRCode(fullLink) {
      try {
        this.qrCompanyCodeDataURL = await this.$qrcode.generate(fullLink);
      } catch (error) {
        console.error("Error generating QR code:", error);
      }
    },

    submit() {
      let employeeFilter = this.employees.filter(
        (employee) => employee.id == this.payload.employee_id
      );

      if (employeeFilter[0]?.branch_id) {
        this.payload.branch_id = employeeFilter[0].branch_id;
      }

      this.$axios
        .post(this.endpoint, this.mapper(Object.assign(this.payload)))
        .then(({ data }) => {
          // this.encrypt(data.record.id);
          this.errors = [];
          this.snackbar = true;
          this.response = "Host inserted successfully";
          this.getDataFromApi();
          this.DialogBox = false;
          this.dialog = true;
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = true;
          this.response = statusText;
        });

      // }
    },

    update_data() {
      let employeeFilter = this.employees.filter(
        (employee) => employee.id == this.payload.employee_id
      );

      if (employeeFilter[0]?.branch_id) {
        this.payload.branch_id = employeeFilter[0].branch_id;
      }
      this.$axios
        .post(
          this.endpoint + "/" + this.payload.id,
          this.mapper(Object.assign(this.payload))
        )
        .then(({ data }) => {
          this.errors = [];
          this.snackbar = true;
          this.response = "Host updated successfully";
          this.getDataFromApi();
          this.DialogBox = false;
        })
        .catch(({ response }) => {
          if (!response) {
            return false;
          }
          let { status, data, statusText } = response;

          if (status && status == 422) {
            this.errors = data.errors;
            return;
          }

          this.snackbar = true;
          this.response = statusText;
        });

      // }
    },
    // processImage(file) {
    //   const canvas = this.$refs.cropper.getCroppedCanvas();
    //   this.cropedImage = canvas.toDataURL();

    //   canvas.toBlob((blob) => {
    //     return { value: blob, file };
    //   }, "image/jpeg");
    // },
  },
};
</script>
