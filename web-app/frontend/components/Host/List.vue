<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-dialog persistent v-model="dialogCropping" width="500">
        <v-card style="padding-top: 20px">
          <v-card-text>
            <VueCropper
              v-show="selectedFile"
              ref="cropper"
              :src="selectedFile"
              alt="Source Image"
              :aspectRatio="1"
              :autoCropArea="0.9"
              :viewMode="3"
            ></VueCropper>
          </v-card-text>

          <v-card-actions>
            <div col="6" md="6" class="col-sm-12 col-md-6 col-12 pull-left">
              <v-btn
                class="danger btn btn-danger text-left"
                text
                @click="closePopup()"
                style="float: left"
                >Cancel</v-btn
              >
            </div>
            <div col="6" md="6" class="col-sm-12 col-md-6 col-12 text-right">
              <v-btn
                class="primary btn btn-danger text-right"
                @click="saveCroppedImageStep2(), (dialog = false)"
                >Crop</v-btn
              >
            </div>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-dialog persistent v-model="DialogBox" width="1100">
        <v-card>
          <v-toolbar class="popup_background" flat>
            {{ formAction }} Company
          </v-toolbar>
          <v-container>
            <v-row>
              <v-col cols="3" class="pt-5">
                <div class="form-group" style="margin: 0 auto; width: 200px">
                  <v-img
                    style="
                      width: 100%;
                      height: 200px;
                      border: 1px solid #5fafa3;
                      border-radius: 50%;
                      margin: 0 auto;
                    "
                    :src="previewImage || '/no-profile-image.jpg'"
                  ></v-img>
                  <br />
                  <v-btn
                    :disabled="disabled"
                    small
                    class="form-control primary"
                    @click="onpick_attachment"
                    >{{ !upload.name ? "Upload" : "Change" }} Company Logo
                    <v-icon right dark>mdi-cloud-upload</v-icon>
                  </v-btn>
                  <input
                    required
                    type="file"
                    @change="attachment"
                    style="display: none"
                    accept="image/*"
                    ref="attachment_input"
                  />

                  <span v-if="errors && errors.logo" class="text-danger mt-2">{{
                    errors.logo[0]
                  }}</span>
                </div>

                <v-text-field
                  :disabled="disabled"
                  type="email"
                  class="mt-5"
                  placeholder="Email Address"
                  v-model="payload.email"
                  dense
                  menu-props="min-width: auto; max-height: 200px;"
                  outlined
                  :hide-details="!errors.email"
                  :error="errors.email"
                  :error-messages="
                    errors && errors.email ? errors.email[0] : ''
                  "
                ></v-text-field>
                <div class="text-center">
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

              <v-col cols="9" class="pt-5">
                <v-row>
                  <v-col cols="6">
                    <label class="col-form-label"
                      >Company Name<span class="text-danger">*</span></label
                    >
                    <v-text-field
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
                  </v-col>

                  <v-col cols="6">
                    <label class="col-form-label"
                      >Manager Name<span class="text-danger">*</span></label
                    >
                    <v-text-field
                      :disabled="disabled"
                      v-model="payload.manager_name"
                      dense
                      menu-props="min-width: auto; max-height: 200px;"
                      class="text-center"
                      outlined
                      :hide-details="!errors.manager_name"
                      :error="errors.manager_name"
                      :error-messages="
                        errors && errors.manager_name
                          ? errors.manager_name[0]
                          : ''
                      "
                    ></v-text-field>
                  </v-col>

                  <v-col cols="6">
                    <label class="col-form-label"
                      >Flat Number<span class="text-danger">*</span></label
                    >
                    <v-text-field
                      :disabled="disabled"
                      v-model="payload.flat_number"
                      dense
                      menu-props="min-width: auto; max-height: 200px;"
                      class="text-center"
                      outlined
                      :hide-details="!errors.flat_number"
                      :error="errors.flat_number"
                      :error-messages="
                        errors && errors.flat_number
                          ? errors.flat_number[0]
                          : ''
                      "
                    ></v-text-field>
                  </v-col>

                  <v-col cols="6">
                    <label class="col-form-label"
                      >Floor Number<span class="text-danger">*</span></label
                    >
                    <v-text-field
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
                        errors && errors.floor_number
                          ? errors.floor_number[0]
                          : ''
                      "
                    ></v-text-field>
                  </v-col>
                </v-row>

                <v-row>
                  <v-col cols="6">
                    <label class="col-form-label">Phone Number</label>
                    <v-text-field
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
                    <label class="col-form-label"
                      >Emergency Phone<span class="text-danger">*</span></label
                    >
                    <v-text-field
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
                </v-row>

                <v-row>
                  <v-col cols="6">
                    <label class="col-form-label"
                      >Office. Open time<span class="text-danger"
                        >*</span
                      ></label
                    >
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
                            errors && errors.open_time
                              ? errors.open_time[0]
                              : ''
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
                    <label class="col-form-label"
                      >Close. Open time<span class="text-danger">*</span></label
                    >
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
                            errors && errors.close_time
                              ? errors.close_time[0]
                              : ''
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

                  <v-col cols="6">
                    <label class="col-form-label"
                      >Zone. Number<span class="text-danger">*</span></label
                    >
                    <v-text-field
                      :disabled="disabled"
                      v-model="payload.zone_id"
                      dense
                      menu-props="min-width: auto; max-height: 200px;"
                      class="text-center"
                      outlined
                      :hide-details="!errors.zone_id"
                      :error="errors.zone_id"
                      :error-messages="
                        errors && errors.zone_id ? errors.zone_id[0] : ''
                      "
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
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
                  </v-col>
                  <v-col cols="12">
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
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-container>
          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>
            <div class="text-right px-3 pb-3">
              <v-btn small color="grey white--text" @click="DialogBox = false">
                Close
              </v-btn>

              <v-btn
                v-if="can('employee_create') && formAction == 'Create'"
                small
                :loading="loading"
                color="primary"
                @click="store_data"
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

      <v-dialog persistent v-model="dialog" max-width="500px">
        <v-card class="white--text">
          <v-card-title dense class="" color="white">
            <!-- Register Link
            <v-icon class="mx-2" color="white">mdi-clipboard-outline</v-icon> -->

            <v-spacer></v-spacer>
            <v-icon @click="dialog = false" outlined color="black">
              mdi-close-circle-outline
            </v-icon>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" class="text-center">
                  <v-avatar v-if="qrCodeDataURL" size="150" tile class="ma-1">
                    <img :src="qrCodeDataURL" alt="Avatar" />
                  </v-avatar>
                </v-col>
                <v-col cols="12" class="text-center">
                  <span>
                    <a :href="`${fullLink}`" target="_blank">
                      {{ fullLink }}
                    </a>
                  </span>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
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
                <v-icon class="ml-2" @click="clearFilters" dark
                  >mdi mdi-reload</v-icon
                >
              </v-btn>
              <!-- </template>
                <span>Reload</span>
              </v-tooltip> -->
              <!-- <v-tooltip top color="primary">
                <template v-slot:activator="{ on, attrs }"> -->
              <v-btn
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Filter"
              >
                <v-icon @click="toggleFilter" class="mx-1 ml-2"
                  >mdi mdi-filter</v-icon
                >
              </v-btn>
              <!-- </template>
                <span>Filter</span>
              </v-tooltip> -->

              <v-spacer></v-spacer>

              <!-- <v-tooltip top color="primary">
                <template v-slot:activator="{ on, attrs }"> -->
              <v-btn
                dense
                x-small
                class="ma-0 px-0"
                :ripple="false"
                text
                title="Add Company"
                @click="addItem"
              >
                <v-icon right size="x-large" dark v-if="can('employee_create')"
                  >mdi-plus-circle</v-icon
                >
              </v-btn>
              <!-- </template>
                <span>Add Company</span>
              </v-tooltip> -->
              <!-- <v-tooltip top color="primary">
                <template v-slot:activator="{ on, attrs }"> -->
              <!-- <v-btn
                dense
                x-small
                class="ma-0 px-0"
                :ripple="false"
                text
                title="QR Code"
                @click="dialog = true"
              >
                <v-icon right size="x-large" dark v-if="can('employee_create')"
                  >mdi-apps</v-icon
                >
              </v-btn> -->
              <!-- </template>
                <span>Test</span>
              </v-tooltip> -->
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
                    </v-container>
                  </td>
                </tr>
              </template>

              <template
                v-slot:item.company_name="{ item, index }"
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
                      :src="item?.logo || '/no-profile-image.jpg'"
                    >
                    </v-img>
                  </v-col>
                  <v-col style="padding: 10px">
                    <strong>
                      {{
                        item.company_name ? item.company_name : "---"
                      }}</strong
                    >
                  </v-col>
                </v-row>
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
        </v-container>
      </div>
    </div>
    <Preloader v-else />
  </div>

  <NoAccess v-else />
</template>

<script>
import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";

export default {
  components: {
    VueCropper,
  },

  data: () => ({
    originalURL: process.env.APP_URL + "/register/visitor/", //`https://mytime2cloud.com/register/visitor/`,
    encryptedID: "",
    fullLink: "",
    qrCodeDataURL: "",
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
        text: "ID",
        align: "left",
        sortable: true,
        key: "id",
        value: "id",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Door No/Flat No",
        align: "left",
        sortable: true,
        key: "flat_number",
        value: "flat_number",
        filterable: true,
        width: "150px",
        filterSpecial: false,
      },
      {
        text: "Company Name",
        align: "left",
        sortable: true,
        key: "company_name",
        value: "company_name",
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Manager Name",
        align: "left",
        sortable: true,
        key: "manager_name",
        value: "manager_name",
        width: "300px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Phone",
        align: "left",
        sortable: true,
        key: "mobile",
        value: "number",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Email",
        align: "left",
        sortable: true,
        key: "email",
        value: "email",
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Office Openning Time",
        align: "left",
        sortable: true,
        key: "open_time",
        value: "open_time",
        filterable: false,
        filterSpecial: false,
      },
      {
        text: "Close Openning Time",
        align: "left",
        sortable: true,
        key: "close_time",
        value: "close_time",
        filterable: false,
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
        text: "Details",
        align: "left",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
    formAction: "Create",
  }),

  async created() {
    this.loading = false;
    this.boilerplate = true;

    this.payloadOptions = {
      params: {
        per_page: 10,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDataFromApi();
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
    json_to_csv(json) {
      let data = json.map((e) => ({
        first_name: e.first_name,
        last_name: e.last_name,
        display_name: e.display_name,
        email: e.user.email,
        phone_number: e.phone_number,
        whatsapp_number: e.whatsapp_number,
        phone_relative_number: e.phone_relative_number,
        whatsapp_relative_number: e.whatsapp_relative_number,
        employee_id: e.employee_id,
        joining_date: e.show_joining_date,
        department_code: e.department_id,
        designation_code: e.designation_id,
        department: e.department.name,
        designation: e.designation.name,
      }));
      let header = Object.keys(data[0]).join(",") + "\n";
      let rows = "";
      data.forEach((e) => {
        rows += Object.values(e).join(",").trim() + "\n";
      });
      return header + rows;
    },
    export_submit() {
      if (this.data.length == 0) {
        this.snackbar = true;
        this.response = "No record to download";
        return;
      }

      let csvData = this.json_to_csv(this.data);
      let element = document.createElement("a");
      element.setAttribute(
        "href",
        "data:text/csv;charset=utf-8, " + encodeURIComponent(csvData)
      );
      element.setAttribute("download", "download.csv");
      document.body.appendChild(element);
      element.click();
      document.body.removeChild(element);
    },
    importEmployee() {
      let payload = new FormData();
      payload.append("employees", this.files);
      payload.append("company_id", this.$auth?.user?.company?.id);
      let options = {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      };
      this.btnLoader = true;
      this.$axios
        .post("/employee/import", payload, options)
        .then(({ data }) => {
          this.btnLoader = false;
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees imported successfully";
            this.getDataFromApi();
            this.close();
          }
        })
        .catch((e) => {
          if (e.toString().includes("Error: Network Error")) {
            this.errors = [
              "File is modified.Please cancel the current file and try again",
            ];
            this.btnLoader = false;
          }
        });
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
      this.disabled = false;
      this.formAction = "Create";
      this.DialogBox = true;
      this.payload = {};
    },
    editItem(item) {
      this.disabled = false;
      this.formAction = "Edit";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
    },
    viewItem(item) {
      this.disabled = true;
      this.formAction = "View";
      this.DialogBox = true;
      this.payload = item;
      this.previewImage = item.logo;
      this.fullLink =
        this.originalURL + this.$auth.user.company_id + "-" + item.id;
      this.generateQRCode(this.fullLink);
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

    store_data() {
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
