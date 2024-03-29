<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
      <v-snackbar v-model="snack" :color="snackColor">
        {{ snackText }}

        <template v-slot:action="{ attrs }">
          <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
        </template>
      </v-snackbar>
    </div>
    <v-dialog persistent v-model="editDialog" width="1250" :key="employeeId">
      <v-card>
        <v-tabs v-model="tab" icons-and-text right color="violet">
          <v-tabs-slider></v-tabs-slider>

          <v-tab v-for="(item, index) in tabMenu" :key="index">
            {{ item.text }}
            <v-icon>{{ item.icon }}</v-icon>
          </v-tab>

          <v-icon
            @click="editDialog = false"
            style="margin-right: 4px"
            outlined
            dark
            color="black"
          >
            mdi mdi-close-circle
          </v-icon>
        </v-tabs>

        <v-card-text>
          <v-tabs-items v-model="tab">
            <v-tab-item v-for="(item, index) in tabMenu" :key="index">
              <component
                :is="getComponent(item.value)"
                :employeeId="employeeId"
                @close-popup="handleEventFromChild"
                v-if="tab == item.value"
              />
            </v-tab-item>
          </v-tabs-items>
        </v-card-text>
      </v-card>
    </v-dialog>
    <div v-if="!loading">
      <v-card elevation="0" class="mt-2">
        <v-toolbar class="mb-2 white--text" color="white" dense flat>
          <v-toolbar-title>
            <span style="color: black"> {{ Model }} </span></v-toolbar-title
          >
          <span
            ><v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Filter"
            >
              <v-icon @click="handleEventFromChild" class="mx-1 ml-2"
                >mdi-reload</v-icon
              >
            </v-btn>
          </span>
          <span
            ><v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Filter"
            >
              <v-icon @click="toggleFilter" class="mx-1 ml-2"
                >mdi-filter</v-icon
              >
            </v-btn>
          </span>
          <v-spacer></v-spacer>
          <v-btn text @click="process_file('print_pdf')">
            <img src="/icons/icon_print.png" class="iconsize" />
          </v-btn>
        </v-toolbar>
        <v-data-table
          dense
          v-model="selectedItems"
          :headers="headers_table"
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
                <v-container style="padding-left: 0px !important">
                  <v-text-field
                    clearable
                    @click:clear="
                      filters[header.value] = '';
                      getDataFromApi();
                    "
                    :hide-details="true"
                    v-if="header.filterable && !header.filterSpecial"
                    v-model="filters[header.value]"
                    :id="header.value"
                    @input="getDataFromApi()"
                    outlined
                    dense
                    autocomplete="off"
                  ></v-text-field>

                  <v-select
                    clearable
                    @click:clear="
                      filters[header.value] = '';
                      getDataFromApi();
                    "
                    :id="header.key"
                    :hide-details="true"
                    v-if="
                      header.filterSpecial &&
                      header.value == 'department_name_id'
                    "
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="name"
                    item-value="id"
                    :items="[
                      { name: `All Departments`, id: `` },
                      ...departments,
                    ]"
                    placeholder="Department"
                    solo
                    flat
                    @change="getDataFromApi()"
                  ></v-select>
                  <v-select
                    clearable
                    @click:clear="
                      filters[header.value] = '';
                      getDataFromApi();
                    "
                    :id="header.key"
                    :hide-details="true"
                    v-if="
                      header.filterSpecial &&
                      header.value == 'branch.branch_name'
                    "
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="name"
                    item-value="id"
                    :items="[{ name: `All Branches`, id: `` }, ...branchList]"
                    placeholder="All Branches"
                    solo
                    flat
                    @change="applyFilters(filters[header.key])"
                  ></v-select>
                  <v-select
                    clearable
                    @click:clear="
                      filters[header.value] = '';
                      getDataFromApi();
                    "
                    :id="header.key"
                    :hide-details="true"
                    v-if="
                      header.filterSpecial && header.value == 'timezone.name'
                    "
                    outlined
                    dense
                    small
                    v-model="filters[header.key]"
                    item-text="name"
                    item-value="id"
                    :items="[
                      {
                        name: `All Timezones`,
                        id: ``,
                      },
                      ...timezones,
                    ]"
                    placeholder="Timezone"
                    solo
                    flat
                    @change="getDataFromApi()"
                  ></v-select>
                </v-container>
              </td>
            </tr>
          </template>

          <template v-slot:item.branch.branch_name="{ item }">
            <div class="my-1" style="width: 70px">
              {{ item?.branch?.branch_name }}
            </div>
          </template>

          <template v-slot:item.employee_id="{ item }">
            <div class="my-1" style="width: 70px">
              <strong>{{ item.employee_id }} </strong><br /><span
                style="font-size: 12px"
                >{{ item.system_user_id }}</span
              >
            </div>
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
                    border-radius: 50%;
                    height: auto;
                    width: 50px;
                    max-width: 50px;
                  "
                  :src="
                    item.profile_picture
                      ? item.profile_picture
                      : '/no-profile-image.jpg'
                  "
                >
                </v-img>
              </v-col>
              <v-col style="padding: 10px">
                <strong>
                  {{ item.first_name ? item.first_name : "---" }}
                  {{ item.last_name ? item.last_name : "---" }}</strong
                >
                <div class="secondary-value">
                  {{ item.designation ? caps(item.designation.name) : "---" }}
                </div>
              </v-col>
            </v-row>
          </template>

          <template v-slot:item.department_name_id="{ item }">
            <strong>{{ caps(item.department.name) }}</strong>
            <div>{{ caps(item.sub_department.name) }}</div>
          </template>

          <template v-slot:item.document="{ item }">
            <div class="my-1" v-if="item.passport" style="width: 300px">
              <b>Passport</b>
              {{ item?.passport?.expiry_date }}
            </div>
            <div class="my-1" v-if="item.emirate" style="width: 300px">
              <b>Emirates Id</b>
              {{ item?.emirate?.expiry }}
            </div>
            <div v-if="item.visa" style="width: 300px">
              <b>Visa</b> {{ item?.visa?.expiry_date }}
            </div>
            <!-- <div v-if="item.visa" style="width: 300px">
              <b>Labour Card</b> {{ item?.visa?.labour_expiry_date }}
            </div> -->
          </template>

          <template v-slot:item.user_email="{ item }">
            <div>{{ item?.user?.email }}</div>
          </template>

          <template v-slot:item.options="{ item }">
            <v-menu bottom left>
              <template v-slot:activator="{ on, attrs }">
                <v-btn dark-2 icon v-bind="attrs" v-on="on">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list width="120" dense>
                <v-list-item
                  v-if="can('employee_edit')"
                  @click="editItem(item)"
                >
                  <v-list-item-title style="cursor: pointer">
                    <v-icon color="secondary" small> mdi-pencil </v-icon>
                    Edit
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
import Passport from "../components/employee/Passport.vue";
import Emirates from "../components/employee/Emirates.vue";
import Visa from "../components/employee/Visa.vue";

import "cropperjs/dist/cropper.css";

export default {
  components: {
    Passport,
    Emirates,
    Visa,
  },

  data: () => ({
    totalRowsCount: 0,
    refresh: true,
    id: "",
    employee_id: "",
    system_user_id: "",
    shifts: [],
    timezones: [],
    joiningDate: null,
    joiningDateMenuOpen: false,
    showFilters: false,
    filters: {},
    isFilter: false,
    sortBy: "employee_id",
    sortDesc: false,
    server_datatable_totalItems: 1000,
    snack: false,
    snackColor: "",
    snackText: "",
    selectedItems: [],
    datatable_search_textbox: "",
    datatable_searchById: "",
    loadinglinear: true,
    displayErrormsg: false,
    image: "",
    mime_type: "",
    cropedImage: "",
    cropper: "",
    autoCrop: false,
    dialogCropping: false,
    comp: "Passport",
    tabMenu: [
      {
        text: "Passport Expiry",
        icon: "mdi-file-powerpoint-outline",
        value: 0,
      },
      {
        text: "Emirates",
        icon: "mdi-city-variant",
        value: 1,
      },
      {
        text: "Visa",
        icon: "mdi-file-document-multiple",
        value: 2,
      },
    ],
    tab: 0,
    employeeId: 0,
    employee_id: 0,
    employeeObject: {},
    attrs: [],
    dialog: false,
    editDialog: false,
    viewDialog: false,
    selectedFile: "",
    employeeDialog: false,
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
    loading: false,
    //total: 0,
    per_page: 1000,
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
    payload: {},
    personalItem: {},
    contactItem: {},
    emirateItems: {},
    setting: {},
    options: {},
    Model: "Document Expiry",
    endpoint: "document_expiry",
    snackbar: false,
    ids: [],
    loading: false,
    //total: 0,
    headers: [],
    titleItems: ["Mr", "Mrs", "Miss", "Ms", "Dr"],
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    departments: [],
    sub_departments: [],
    designations: [],
    roles: [],
    department_filter_id: "",
    dialogVisible: false,
    payloadOptions: {},
    headers_table: [
      {
        text: "Emp Id / Device Id",
        align: "left",
        sortable: true,
        key: "employee_id",
        value: "employee_id",
        filterable: true,
        width: "150px",
        filterSpecial: false,
      },
      {
        text: "Name",
        align: "left",
        sortable: true,
        key: "first_name",
        value: "first_name",
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Phone Number",
        align: "left",
        sortable: true,
        key: "phone_number",
        value: "phone_number",
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Email",
        align: "left",
        sortable: true,
        key: "user_email",
        value: "user_email",
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Document",
        align: "left",
        sortable: true,
        key: "document",
        value: "document", //template name should be match for sorting sub table should be the same
        width: "200px",
        filterable: true,
        filterSpecial: true,
      },

      {
        text: "Options",
        align: "left",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
    branchList: [],
    branch_id: null,
    isCompany: true,
    import_branch_id: "",

    refresh: false,
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.employee.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      await this.getDepartments(this.branch_id);
      return;
    }
    this.headers_table.splice(0, 0, {
      text: "Branch",
      align: "left",
      sortable: true,
      key: "branch_id",
      value: "branch.branch_name",
      filterable: true,
      filterSpecial: true,
    });

    if (!this.data) {
      this.refresh = true;
      await this.getDataFromApi();
    }
  },
  mounted() {
    //this.getDataFromApi();
    this.headers = [
      // { text: "#" },
      { text: "E.ID" },
      { text: "Profile" },
      { text: "Name" },
      { text: "Email" },
      { text: "Timezone" },
      { text: "Dept" },
      { text: "Sub Dept" },
      { text: "Desgnation" },
      { text: "Role" },
      { text: "Mobile" },
      { text: "Shift" },
      { text: "Actions" },
    ];
  },
  watch: {
    options: {
      async handler() {
        await this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    async process_file(type) {
      try {
        if (!this.data || !this.data.length) {
          alert("No data found");
          return;
        }

        const backendUrl = process.env.BACKEND_URL;

        const queryParams = {
          company_id: this.$auth.user.company_id,
          ...this.filters
        };

        const queryString = Object.keys(queryParams)
          .map(
            (key) =>
              `${encodeURIComponent(key)}=${encodeURIComponent(
                queryParams[key]
              )}`
          )
          .join("&");

        const reportUrl = `${backendUrl}/document_expiry_${type.toLowerCase()}?${queryString}`;

        console.log(reportUrl);

        // return;

        const report = document.createElement("a");
        report.setAttribute("href", reportUrl);
        report.setAttribute("target", "_blank");
        report.click();
      } catch (error) {
        console.error("Error processing file:", error.message);
        // Handle the error (e.g., show an error message to the user)
      }
    },
    async handleChangeEvent() {
      this.branchList = await this.$store.dispatch("fetchDropDowns", {
        key: "branchList",
        endpoint: "branch-list",
      });
    },
    getComponent(value) {
      const componentsList = {
        0: "Passport",
        1: "Emirates",
        2: "Visa",
      };
      return componentsList[value] || "div"; // default to a div if no component found
    },
    async handleEventFromChild() {
      this.refresh = true;
      await this.getDataFromApi();
      this.editDialog = false;
    },
    async openNewPage() {
      this.employee = {};
      this.departments = [];
      this.employeeDialog = true;

      if (this.$auth.user.branch_id) {
        await this.getDepartments(this.$auth.user.branch_id);
      } else {
        await this.getDepartments(null);
      }

      await this.handleChangeEvent();
    },
    async filterDepartmentsByBranch(filterBranchId) {
      await this.getDepartments(filterBranchId);
      await this.getTimezone(filterBranchId);
    },
    async getDepartments(filterBranchId) {
      let options = {
        endpoint: "department-list",
        isFilter: this.isFilter,
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: filterBranchId,
        },
      };
      this.departments = await this.$store.dispatch("department_list", options);
    },

    async getTimezone(filterBranchId) {
      let options = {
        endpoint: "timezone-list",
        isFilter: this.isFilter,
        params: {
          company_id: this.$auth.user.company_id,
          branch_id: filterBranchId,
        },
      };
      this.timezones = await this.$store.dispatch("timezone_list", options);
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
    async toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;

      if (this.isFilter) {
        this.refresh = true;
        this.handleChangeEvent();
      }
    },
    async applyFilters(id) {
      this.refresh = true;
      await this.getDataFromApi();
      await this.getDepartments(id);
      await this.getTimezone(id);
    },
    async getDataFromApi() {
      this.loadinglinear = true;

      const data = await this.$store.dispatch("fetchData", {
        key: "employees_with_document_expiry",
        options: this.options,
        refresh: this.refresh,
        endpoint: this.endpoint,
        filters: this.filters,
      });

      this.data = data.data;
      this.totalRowsCount = data.total;
      this.loadinglinear = false;
    },

    editItem(item) {
      this.employeeId = item.id;
      this.editDialog = true;
    },
    viewItem(item) {
      this.employeeId = item.id;

      this.system_user_id = item.system_user_id;
      this.employee_id = item.employee_id;

      this.employeeObject = item;
      this.viewDialog = true;
    },
    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
  },
};
</script>
