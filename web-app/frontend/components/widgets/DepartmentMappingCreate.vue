<template>
  <v-row justify="center">
    <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
      {{ response }}
    </v-snackbar>
    <v-dialog persistent v-model="dialog" max-width="700px">
      <v-card>
        <v-card-title class="popup_background" dark dense>
          <span class="text-h5">Department Mapping</span>
          <v-spacer></v-spacer>
          <v-icon @click="close" outlined dark>
            mdi-close-circle-outline
          </v-icon>
        </v-card-title>
        <v-card-text class="py-2">
          <v-container>
            <v-row>
              <v-col cols="12" sm="6" md="6">
                <v-text-field
                  outlined
                  dense
                  label="Title"
                  v-model="editedItem.title"
                  :hide-details="true"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-autocomplete
                  :hide-details="true"
                  v-model="editedItem.departments"
                  :items="departments"
                  item-text="name"
                  item-value="id"
                  dense
                  outlined
                  label="Departments"
                  multiple
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="search"
                  label="Search Employees"
                  dense
                  outlined
                  clearable
                ></v-text-field>
                <v-card
                  outlined
                  style="min-height: 300px; max-height: 300px; overflow-y: auto"
                >
                  <v-card-text>
                    <ComonPreloader icon="face-scan" v-if="loading" />
                    <div v-else-if="!loading && !departmentEmployees.length">
                      No record found
                    </div>
                    <v-treeview
                      v-else
                      dense
                      hoverable
                      :selection-type="treeType"
                      :open="treeviewOpen"
                      :search="search"
                      selected-color="background"
                      selectable
                      v-model="editedItem.employees"
                      :items="filteredTree"
                    ></v-treeview>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn small color="background" dark @click="submit">
            {{ isExisted == true ? "Update" : "Save" }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
export default {
  props: {
    editedItem: Object,
    isExisted: Boolean,
    dialog: Boolean,

    title: String,
    message: String,
  },
  data() {
    return {
      response: "",
      snackbar: false,
      loading: true,
      dialogVisible: false,
      selectedDepartments: [],
      departments: [],
      departmentEmployees: [],
      treeviewOpen: [],
      search: "",
      treeType: "leaf",
    };
  },
  computed: {
    filteredTree() {
      if (!this.search) {
        this.treeType = "leaf";
        return this.departmentEmployees;
      }
      this.treeType = "independent";

      const filtered = this.departmentEmployees.map((parent) => {
        const children = parent.children.filter(
          ({ id, name }) =>
            name.toLowerCase().includes(this.search.toLowerCase()) ||
            this.editedItem.employees.includes(id)
        );

        return { ...parent, children };
      });

      this.treeviewOpen = filtered.map((parent) => parent.id);

      return filtered.filter((parent) => parent.children.length > 0);
    },
  },
  created() {
    this.editedItem.company_id = this.$auth.user.company_id;

    let options = {
      params: {
        per_page: 1000,
        company_id: this.editedItem.company_id,
      },
    };
    this.getDepartments(options);
    this.getDepartmentEmployees(options);
  },
  methods: {
    getDepartments(options) {
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;
      });
    },
    getDepartmentEmployees(options) {
      this.$axios.get("department-employee", options).then(async ({ data }) => {
        this.departmentEmployees = await data.data.map((e) => ({
          id: e.id,
          name: e.name,
          children: e.employees,
        }));
        this.loading = false;
      });
    },

    submit() {
      let endpoint = "assigned-department-employee";
      if (this.isExisted) {
        this.$axios
          .put(endpoint + "/" + this.editedItem.id, this.editedItem)
          .then(({ data }) => this.handleData(data))
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(endpoint, this.editedItem)
          .then(({ data }) => this.handleData(data))
          .catch((res) => console.log(res));
      }
    },
    handleData(data) {
      if (!data.status) {
        this.errors = data.errors;
        return;
      }
      this.$emit("submitted");
      this.snackbar = true;
      this.response = data.message;
      this.errors = [];
      this.search = "";
      this.close();
    },
    close() {
      this.dialog = false;
      this.$emit("close");
    },
  },
};
</script>
