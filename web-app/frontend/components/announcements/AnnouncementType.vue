<template>
  <div v-if="can(`announcement_category_access`)">
    <div class="text-center ma-2">
      <v-snackbar
        v-model="snackbar"
        top="top"
        :color="snackColor"
        elevation="24"
      >
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog v-model="dialog" width="500px">
      <v-card>
        <v-card-title dense class="popup_background">
          {{ formTitle }}
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <label for="" style="margin-bottom: 5px">Name</label> <br />
                <v-text-field
                  dense
                  outlined
                  v-model="editedItem.name"
                  placeholder="Name"
                  :error-messages="errors && errors.name ? errors.name[0] : ''"
                ></v-text-field>
              </v-col>

              <!-- <v-col cols="12">
                <label for="" mb-1 style="margin-bottom: 5px"
                  >Descriptoin</label
                >
                <v-text-field
                  dense
                  outlined
                  v-model="editedItem.description"
                  placeholder="Descriptoin"
                  :error-messages="
                    errors && errors.description ? errors.description[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="12">
                <label for="" mb-1 style="margin-bottom: 5px">Priority</label>

                <v-select
                  :items="[1, 2, 3]"
                  dense
                  outlined
                  v-model="editedItem.priority"
                  placeholder="Priority"
                  :error-messages="
                    errors && errors.priority ? errors.priority[0] : ''
                  "
                ></v-select>
              </v-col> -->
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <!-- <Back color="primary" /> -->

        <v-card class="mb-5 mt-2 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> {{ Model }}</span></v-toolbar-title
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
                <v-icon class="ml-2" @click="getDataFromApi()" dark
                  >mdi-reload</v-icon
                >
              </v-btn>
            </span>
            <v-spacer></v-spacer>
            <span>
              <v-btn
                v-if="can(`announcement_category_create`)"
                dense
                class="ma-0 px-0"
                x-small
                :ripple="false"
                text
                title="Add Announcement Category"
              >
                <v-icon class="ml-2" @click="newDialog()" dark
                  >mdi mdi-plus-circle</v-icon
                >
              </v-btn>
            </span>
          </v-toolbar>

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            v-if="can(`leave_type_view`)"
            v-model="ids"
            item-key="id"
            :headers="headers"
            :items="data"
            :loading="loading"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
          >
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
                      <v-icon
                        v-if="can(`announcement_category_edit`)"
                        color="secondary"
                        small
                        @click="editItem(item)"
                      >
                        mdi-pencil
                      </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`announcement_category_delete`)"
                        color="error"
                        small
                        @click="deleteItem(item)"
                      >
                        {{
                          item.announcement === "customer" ? "" : "mdi-delete"
                        }}
                      </v-icon>
                      Delete
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>

            <template v-slot:no-data>
              <!-- <v-btn color="primary" @click="initialize">Reset</v-btn> -->
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
import { TiptapVuetify } from "tiptap-vuetify";

export default {
  components: {
    TiptapVuetify,
  },
  data: () => ({
    formTitle: "New Category",
    dialogEmployees: false,
    idsEmployeeList: [],
    //editor
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

    title: "",

    options: {},
    Model: "Announcement Categories",
    endpoint: "announcements_category",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],
    loading: false,
    total: 0,

    headers: [
      {
        text: "Name",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
      },
      // {
      //   text: "Description",
      //   align: "left",
      //   sortable: true,
      //   value: "description",
      // },
      // {
      //   text: "Priority",
      //   align: "left",
      //   sortable: true,
      //   value: "priority",
      // },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      name: "",
      description: "",
    },
    defaultItem: {
      name: "",
      description: "",
    },
    response: "",
    data: [],
    errors: [],
    branch_id: null,
    branchesList: [],
  }),

  computed: {},

  watch: {},
  created() {
    this.loading = true;

    this.options = {
      params: {
        per_page: 100,
        company_id: this.$auth.user.company_id,
      },
    };

    if (this.$auth.user.branch_id == null) {
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
      this.headers.splice(1, 0, ...branch_header);
    }

    this.$axios.get(`branches_list`, this.options).then(({ data }) => {
      this.branchesList = data;
      this.branch_id = this.$auth.user.branch_id || "";
    });

    this.getDataFromApi();
  },

  methods: {
    newDialog() {
      this.formTitle = "New Category";
      this.errors = [];
      this.editedIndex = -1;
      this.editedItem = {
        name: "",
        description: "",
        priority: 0,
      };
      this.defaultItem = {
        name: "",
        description: "",
        priority: 0,
      };
      this.dialog = true;
    },

    can(per) {
      return this.$pagePermission.can(per, this);
    },

    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      let endDate = new Date();

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
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
        this.total = data.total;
        this.loading = false;
      });
    },

    editItem(item) {
      this.errors = [];
      this.formTitle = "Edit Category  ";
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    deleteItem(item) {
      confirm("Are you sure you wish to delete ?") &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
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

    save() {
      this.editedItem.company_id = this.$auth.user.company_id;

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
              this.errors = data.errors;
              this.snackColor = "error";
              this.snackbar = true;
              this.response = data.message;
            } else {
              const index = this.data.findIndex(
                (item) => item.id == this.editedItem.id
              );
              this.data.splice(index, 1, {
                id: this.editedItem.id,
                name: this.editedItem.name,
              });
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;

              //this.snackbar = true;
              // this.snackColor = "error";
              //this.response = data.message;
              this.loading = false;
            } else {
              this.getDataFromApi();
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
  },
};
</script>
