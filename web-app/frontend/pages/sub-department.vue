<template>
  <div v-if="can(`sub_department_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-row class=" ">
      <v-col cols="6">
        <h3>{{ Model }}</h3>
        <div>Dashboard / {{ Model }}</div>
      </v-col>
      <v-col>
        <div class="display-1 pa-2 text-right">
          <v-btn small class="primary" to="/department">
            <v-icon small>mdi-arrow-left</v-icon>&nbsp;Back
          </v-btn>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-dialog
        persistent
        v-model="dialogForm"
        :fullscreen="false"
        width="500px"
      >
        <v-card elevation="0">
          <v-toolbar color="background" dense flat dark>
            <span>{{ formTitle }} {{ Model }}</span>
          </v-toolbar>
          <v-divider class="py-0 my-0"></v-divider>
          <v-card-text>
            <v-container>
              <v-row class="mt-2">
                <v-col cols="12">
                  <v-text-field
                    v-model="editedItem.name"
                    placeholder="Sub Department"
                    outlined
                    dense
                  ></v-text-field>
                  <span v-if="errors && errors.name" class="error--text">{{
                    errors.name[0]
                  }}</span>
                </v-col>
                <v-col cols="12">
                  <v-autocomplete
                    v-model="editedItem.department_id"
                    :items="departments"
                    item-text="name"
                    item-value="id"
                    placeholder="Select Departments"
                    outlined
                    dense
                  >
                  </v-autocomplete>
                  <span
                    v-if="errors && errors.department_id"
                    class="error--text"
                    >{{ errors.department_id[0] }}</span
                  >
                </v-col>

                <v-card-actions>
                  <v-col md="6" lg="6" style="padding: 0px">
                    <v-btn class="error" @click="close"> Cancel </v-btn></v-col
                  >
                  <v-col md="6" lg="6" class="text-right" style="padding: 0px">
                    <v-btn class="primary" @click="save">Save</v-btn>
                  </v-col>
                </v-card-actions>
              </v-row>
            </v-container>
          </v-card-text>
        </v-card>
      </v-dialog>

      <v-col md="12" lg="12">
        <v-card class="mb-5 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" color="background" dense flat dark>
            <v-toolbar-title
              ><span> {{ Model }} List</span></v-toolbar-title
            >
            <v-spacer></v-spacer>
            <v-toolbar-items>
              <v-col>
                <v-btn
                  @click="newItem"
                  small
                  class="primary mr-2 mb-2 toolbar-button-design1"
                  color="primary"
                  >Add {{ Model }} +
                </v-btn>
              </v-col>
            </v-toolbar-items>
          </v-toolbar>
          <!-- <v-text-field class="form-control py-0 ma-1 mb-0 w-25 float-start custom-text-box floating shadow-none"
            placeholder="Search..." solo flat @input="searchIt" v-model="search" :hide-details="true"></v-text-field> -->

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            dense
            :headers="headers_table"
            :items="data"
            model-value="data.id"
            :loading="loading"
            :footer-props="{
              itemsPerPageOptions: [50, 100, 500, 1000],
            }"
            class="elevation-1"
          >
            <template v-slot:item.sno="{ item, index }">
              {{ ++index }}
            </template>

            <template v-slot:item.name="{ item }">
              <v-edit-dialog
                large
                save-text="Reset"
                cancel-text="Ok"
                style="margin-left: 4%"
                @save="getDataFromApi()"
                @open="datatable_open"
              >
                {{ caps(item.name) }}
                <template v-slot:input>
                  <v-text-field
                    @input="
                      getDataFromApi('', 'serach_sub_department_name', $event)
                    "
                    v-model="datatable_search_textbox"
                    label="Search Sub Department name"
                  ></v-text-field>
                </template>
              </v-edit-dialog>
            </template>
            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
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

          <!-- <table>
            <tr>
              <th class="ps-5">#</th>
              <th>Sub Department</th>
              <th>Department</th>
              <th class="text-center">Action</th>
            </tr>
            <v-progress-linear v-if="loading" :active="loading" :indeterminate="loading" absolute
              color="primary"></v-progress-linear>
            <tr v-for="(item, index) in data" :key="index">
              <td class="ps-5">
                <b>{{ ++index }}</b>
              </td>
              <td>{{ caps(item.name || "---") }}</td>
              <td>{{ caps(item.department.name) }}</td>
              <td class="text-center">
                <v-menu bottom left>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn dark-2 icon v-bind="attrs" v-on="on">
                      <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
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
              </td>
            </tr>
          </table> -->
        </v-card>
      </v-col>
    </v-row>
    <div>
      <v-row>
        <v-col md="12" class="float-right">
          <div class="float-right">
            <v-pagination
              v-model="pagination.current"
              :length="pagination.total"
              @input="onPageChange"
              :total-visible="12"
            ></v-pagination>
          </div>
        </v-col>
      </v-row>
    </div>
  </div>
  <NoAccess v-else />
  <NoAccess v-else />
</template>
<script>
export default {
  data: () => ({
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    dialogForm: false,
    requestDepartmentid: "",
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    Model: "Sub Departments",
    options: {},
    endpoint: "sub-departments",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    headers: [
      {
        text: "Sub Departments",
        align: "left",
        sortable: false,
        value: "name",
      },
      {
        text: "Departments",
        align: "left",
        sortable: false,
        value: "department.name",
      },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    headers_table: [
      {
        text: "#",
        align: "left",
        sortable: true,
        value: "sno",
      },
      {
        text: "Sub Department Name",
        align: "left",
        sortable: true,
        value: "name",
      },
      {
        text: "Department Name ",
        align: "left",
        sortable: false,
        value: "department.name",
      },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: { name: "", department_id: "" },
    defaultItem: { name: "", department_id: "" },
    response: "",
    data: [],
    departments: [],
    errors: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
  },

  watch: {
    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
  },
  created() {
    this.getDepartments();
    this.getDataFromApi();

    this.editedItem.department_id = parseInt(this.$route.query.id);
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
    newItem() {
      this.dialogForm = true;
    },
    onPageChange() {
      this.getDataFromApi();
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    getDepartments() {
      let options = {
        params: {
          per_page: 100,
          company_id: this.$auth.user.company_id,
          department_id: this.$route.query.id,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;

        this.editedItem.department_id = parseInt(this.$route.query.id);
      });
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    

    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      let page = this.pagination.current;

      let options = {
        params: {
          per_page: this.pagination.per_page,
          company_id: this.$auth.user.company_id,
          department_id: this.$route.query.id,
        },
      };
      if (filter_column != "") {
        options.params[filter_column] = filter_value;
      }
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          return false;
        }
        this.data = data.data;
        this.pagination.current = data.current_page;
        this.pagination.total = data.last_page;
        this.loading = false;
      });
    },

    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.dialogForm = true;
    },

    delteteSelectedRecords() {
      let just_ids = this.ids.map((e) => e.id);
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
            ids: just_ids,
          })
          .then((res) => {
            if (!res.data.status) {
              this.errors = res.data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = res.data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
          })
          .catch((err) => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.dialog = false;
      this.dialogForm = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      let payload = {
        name: this.editedItem.name.toLowerCase(),
        department_id: this.editedItem.department_id,
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
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.dialogForm = false;
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
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
              this.dialogForm = false;
            }
          })
          .catch((res) => console.log(res));
      }
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  text-align: left;
  padding: 5px;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

.toolbar-button-design {
  height: 38px !important;
  /* vertical-align: bottom; */
  margin: auto;
  border-radius: 5px;
}
</style>
