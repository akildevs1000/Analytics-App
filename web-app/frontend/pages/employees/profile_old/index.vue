<template>
  <div v-if="can('employee_access')">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" small top="top" :color="color">
        {{ response }}
      </v-snackbar>
    </div>
    <div v-if="!loading">
      <v-row>
        <v-col cols="12">
          <h3>Profile</h3>
          <div>Dashboard / Profile</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="3">
          <v-row>
            <v-col cols="12" class="text-center">
              <v-card>
                <v-card-text>
                  <v-avatar size="150" class="mx-auto">
                    <v-img
                      :src="test_employee.photoUrl"
                      alt="Employee Photo"
                      contain
                    ></v-img>
                  </v-avatar>
                </v-card-text>
                <div>
                  <strong>{{ test_employee.name }} </strong>
                  <v-card-subtitle>{{
                    test_employee.position
                  }}</v-card-subtitle>
                </div>
                <!-- <v-divider></v-divider>
            <v-card-text>
              <v-chip
                class="primary mx-1"
                v-for="(skill, index) in test_employee.skills"
                :key="index"
                >{{ skill }}</v-chip
              >
            </v-card-text> -->
              </v-card>
            </v-col>
            <v-col cols="12">
              <v-card>
                <v-container>
                  <div><strong>Profile Info</strong></div>
                  <v-divider></v-divider>
                  <v-row>
                    <v-col xs="6" sm="2" cols="6"
                      ><v-avatar
                        size="40"
                        color="primary"
                        class="d-flex justify-center align-center"
                      >
                        <v-icon color="white"
                          >mdi-account-card-details-outline</v-icon
                        >
                      </v-avatar></v-col
                    >
                    <v-col xs="6" sm="6" cols="6">
                      <div>Full Name</div>
                      <b>Ms. {{ test_employee.name }}</b></v-col
                    >
                  </v-row>
                  <v-row>
                    <v-col xs="6" sm="2" cols="6"
                      ><v-avatar
                        size="40"
                        color="primary"
                        class="d-flex justify-center align-center"
                      >
                        <v-icon color="white"
                          >mdi-account-card-details-outline</v-icon
                        >
                      </v-avatar></v-col
                    >
                    <v-col xs="6" sm="6" cols="6">
                      <div>Employee Id</div>
                      <b>1111</b></v-col
                    >
                  </v-row>

                  <v-row>
                    <v-col xs="6" sm="2" cols="6"
                      ><v-avatar
                        size="40"
                        color="primary"
                        class="d-flex justify-center align-center"
                      >
                        <v-icon color="white"
                          >mdi-account-card-details-outline</v-icon
                        >
                      </v-avatar></v-col
                    >
                    <v-col xs="6" sm="6" cols="6">
                      <div>Employee Device Id</div>
                      <b>1111</b></v-col
                    >
                  </v-row>

                  <v-row>
                    <v-col xs="6" sm="2" cols="6"
                      ><v-avatar
                        size="40"
                        color="primary"
                        class="d-flex justify-center align-center"
                      >
                        <v-icon color="white">mdi-email</v-icon>
                      </v-avatar></v-col
                    >
                    <v-col xs="6" sm="6" cols="6">
                      <div>Email</div>
                      <b>{{ test_employee.email }}</b></v-col
                    >
                  </v-row>

                  <v-row>
                    <v-col xs="6" sm="2" cols="6"
                      ><v-avatar
                        size="40"
                        color="primary"
                        class="d-flex justify-center align-center"
                      >
                        <v-icon color="white">mdi-domain</v-icon>
                      </v-avatar></v-col
                    >
                    <v-col xs="6" sm="6" cols="6">
                      <div>Department</div>
                      <b>Sales</b></v-col
                    >
                  </v-row>

                  <v-row>
                    <v-col xs="6" sm="2" cols="6"
                      ><v-avatar
                        size="40"
                        color="primary"
                        class="d-flex justify-center align-center"
                      >
                        <v-icon color="white">mdi-phone</v-icon>
                      </v-avatar></v-col
                    >
                    <v-col xs="6" sm="6" cols="6">
                      <div>Phone</div>
                      <b>{{ test_employee.phone }}</b></v-col
                    >
                  </v-row>
                </v-container>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
        <v-col cols="12" md="9">
          <v-row>
            <v-col cols="12">
              <v-card>
                <v-tabs
                  v-model="tab"
                  centered
                  icons-and-text
                  background-color="white"
                >
                  <v-tabs-slider></v-tabs-slider>

                  <v-tab
                    v-for="(item, index) in tabMenu"
                    :key="index"
                    :href="item.value"
                  >
                    {{ item.text }}
                    <v-icon>{{ item.icon }}</v-icon>
                  </v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                  <v-tab-item
                    v-for="(tb, index) in tabMenu"
                    :key="index"
                    :value="`${index}`"
                  >
                    <component
                      :is="getComponent(tab)"
                      :employeeId="employeeId"
                    />
                  </v-tab-item>
                </v-tabs-items>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <v-dialog persistent v-model="dialogCropping" width="500">
        <v-card style="padding-top: 20px">
          <v-card-text>
            <!-- <img :src="imageUrl" alt="Preview Image" /> -->
            <!-- Cropping image step1 -->
            <VueCropper
              v-show="selectedFile"
              ref="cropper"
              :src="selectedFile"
              alt="Source Image"
              :aspectRatio="1"
              :autoCropArea="0.9"
              :viewMode="3"
            ></VueCropper>

            <!-- <div class="cropper-preview"></div> -->
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
    </div>
    <Preloader v-else />
  </div>

  <NoAccess v-else />
</template>

<script>
import EmployeeEdit from "../../../components/employee/EmployeeEdit.vue";
import Contact from "../../../components/employee/Contact.vue";
import Passport from "../../../components/employee/Passport.vue";
import Emirates from "../../../components/employee/Emirates.vue";
import Visa from "../../../components/employee/Visa.vue";
import Bank from "../../../components/employee/Bank.vue";
import Document from "../../../components/employee/Document.vue";
import Qualification from "../../../components/employee/Qualification.vue";
import Setting from "../../../components/employee/Setting.vue";
import Payroll from "../../../components/employee/Payroll.vue";
import Login from "../../../components/employee/Login.vue";

import "cropperjs/dist/cropper.css";
import VueCropper from "vue-cropperjs";
const compList = [
  EmployeeEdit,
  Contact,
  Passport,
  Emirates,
  Visa,
  Bank,
  Document,
  Qualification,
  Setting,
  Payroll,
  Login,
];

export default {
  components: {
    VueCropper,
  },

  data: () => ({
    tab: null,
    text: "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
    test_employee: {
      name: "Sonia Taylor",
      position: "Senior Software Engineer",
      department: "Engineering",
      photoUrl:
        "https://laravelui.spruko.com/valex/build/assets/img/users/6.jpg",
      email: "johndoe@example.com",
      phone: "+1 123-456-7890",
      address: "123 Main Street, City, State, Zip Code",
      skills: ["Java", "Python", "JavaScript"],
    },
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
    compList,
    comp: "EmployeeEdit",
    tabMenu: [],
    tab: "0",
    employeeId: 0,
    employeeDetails: {},
    attrs: [],
    dialog: false,
    editDialog: false,
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
    search: "",
    loading: false,
    total: 0,
    next_page_url: "",
    prev_page_url: "",
    current_page: 1,
    per_page: 8,
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
    payload: {},
    personalItem: {},
    contactItem: {},
    emirateItems: {},
    setting: {},

    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    options: {},
    Model: "Employee",
    endpoint: "employee",
    search: "",
    snackbar: false,
    ids: [],
    loading: false,
    total: 0,
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
        text: "EMP ID",
        align: "left",
        sortable: true,
        key: "employee_id",
        value: "employee_id",
      },
      {
        text: "Name",
        align: "left",
        sortable: true,
        key: "display_name",
        value: "display_name",
      },
      // { text: "Name", align: "left", sortable: true, key: 'display_name', value: "display_name_search_icon" },

      {
        text: "Department",
        align: "left",
        sortable: true,
        key: "department",
        value: "department.name", //template name should be match for sorting sub table should be the same
      },
      {
        text: "Mobile",
        align: "left",
        sortable: true,
        key: "mobile",
        value: "phone_number", // search and sorting enable if value matches with template name
      },
      {
        text: "Email",
        align: "left",
        sortable: true,
        key: "email",
        value: "user.email",
      },
      {
        text: "Shift",
        align: "left",
        sortable: true,
        key: "shceduleshift", //sorting without . _
        value: "schedule.shift.name",
      },
      {
        text: "Timezone",
        align: "left",
        sortable: true,
        key: "timezone",
        value: "timezone.timezone_name",
      },
      {
        text: "Options",
        align: "left",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
  }),
  async created() {
    this.loading = false;
    this.boilerplate = true;

    this.payloadOptions = {
      params: {
        per_page: 1000,
        company_id: this.$auth.user.company_id,
      },
    };

    this.getDepartments();

    this.employeeId = this.$auth.user.employee.id;
  },
  mounted() {
    this.tabMenu = [
      {
        text: "Profile",
        icon: "mdi-account-box",
        value: "#0",
      },
      {
        text: "Contact",
        icon: "mdi-phone",
        value: "#1",
      },
      {
        text: "Passport",
        icon: "mdi-file-powerpoint-outline",
        value: "#2",
      },
      {
        text: "Emirates",
        icon: "mdi-city-variant",
        value: "#3",
      },
      {
        text: "Visa",
        icon: "mdi-file-document-multiple",
        value: "#4",
      },
      {
        text: "Bank",
        icon: "mdi-bank",
        value: "#5",
      },
      {
        text: "Documents",
        icon: "mdi-file",
        value: "#6",
      },
      {
        text: "Qualification",
        icon: "mdi-account-box",
        value: "#7",
      },
      {
        text: "Setting",
        icon: "mdi-phone",
        value: "#8",
      },
      {
        text: "Payroll",
        icon: "mdi-briefcase",
        value: "#9",
      },
      {
        text: "Login",
        icon: "mdi-lock",
        value: "#10",
      },
    ];
    this.headers = [
      // { text: "#" },
      { text: "EID" },
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
    dialog(val) {
      val || this.close();
    },
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
      //this.datatable_search_textbox = '';
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
    getComponent() {
      return this.compList[this.tab];
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
            payload.delete("employees");
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees imported successfully";
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
    
    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
        this.departments.unshift({ name: "All Departments", id: "" });
      });
    },

    close() {
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    save() {
      let payload = {
        name: this.editedItem.name.toLowerCase(),
        company_id: this.$auth.user.company_id,
      };
      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name,
              });
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, payload)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
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
      let employee = new FormData();

      for (let x in obj) {
        employee.append(x, obj[x]);
      }
      employee.append("profile_picture", this.upload.name);
      employee.append("company_id", this.$auth.user.company_id);

      return employee;
    },
    store_data() {
      let final = Object.assign(this.employee);
      let employee = this.mapper(final);

      //croppedimageStep3
      if (this.$refs.attachment_input.files[0]) {
        this.cropedImage = this.$refs.cropper.getCroppedCanvas().toDataURL();

        this.$refs.cropper.getCroppedCanvas().toBlob((blob) => {
          // Create a FormData object and append the Blob as a file
          //const formData = new FormData();
          employee.append("profile_picture", blob, "cropped_image.jpg");
          employee.append("attachment_input", blob, "cropped_image.jpg");

          //croppedimagesptep4 //push to API in blob method only
          this.saveToAPI(employee);
        }, "image/jpeg");
      } else {
        this.saveToAPI(employee);
      }
    },
    saveToAPI(employee) {
      this.$axios
        .post("/employee-store", employee)
        .then(({ data }) => {
          //this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = "Employees inserted successfully";
            this.employeeDialog = false;
          }
        })
        .catch((e) => console.log(e));
    },
  },
};
</script>
