<template>
  <div v-if="can(`payroll_generation_date_access`)">
    <v-card class="mt-2">
      <v-toolbar class="rounded-md" dense flat>
        <v-toolbar-title
          ><span> {{ Module }}</span></v-toolbar-title
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
              >mdi mdi-reload</v-icon
            >
          </v-btn>
        </span>
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
      </v-toolbar>
      <v-data-table
        dense
        :headers="headers"
        :items="data"
        model-value="data.id"
        :loading="loading"
        :options.sync="options"
        :footer-props="{
          itemsPerPageOptions: [10, 50, 100, 500, 1000],
        }"
        class="elevation-1 pt-5"
      >
        <template v-slot:item.sno="{ item, index }">
          {{
            currentPage
              ? (currentPage - 1) * perPage +
                (cumulativeIndex + data.indexOf(item))
              : ""
          }}
        </template>
        <template v-slot:item.action="{ item }">
          <v-menu bottom left>
            <template v-slot:activator="{ on, attrs }">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
            <v-list width="120" dense>
              <!-- <v-list-item @click="viewItem(item)">
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
              </v-list-item> -->
              <v-list-item
                v-if="can(`payroll_generation_date_delete`)"
                @click="deleteItem(item)"
              >
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
  <NoAccess v-else />
</template>

<script>
import Back from "../components/Snippets/Back.vue";
export default {
  components: { Back },
  data: () => ({
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    data: [],
    snack: false,
    snackColor: "",
    snackText: "",

    pagination: {
      current: 1,
      total: 0,
      per_page: 100,
    },
    Module: "Payroll Generate Date",
    options: {},
    endpoint: "payroll_generate_date",
    headers: [
      {
        text: "#",
        align: "left",
        sortable: false,
        key: "sno",
        value: "sno",
      },

      {
        text: "Date",
        align: "left",
        sortable: true,
        key: "date",
        value: "date",
      },
      {
        text: "Action",
        align: "left",
        sortable: false,
        key: "action",
        value: "action",
      },
    ],
    branch_id: null,
    branchesList: [],
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
    this.loading = true;

    if (this.$auth.user.branch_id) {
      this.branch_id = this.$auth.user.branch_id;
      this.isCompany = false;
      return;
    }

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
  },

  methods: {
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      this.loading = true;

      let page = this.pagination.current;

      let options = {
        params: {
          per_page: this.pagination.per_page,
          page: page,
          company_id: this.$auth.user.company_id,
          branch_id: this.branch_id,
        },
      };
      if (filter_column != "") {
        options.params["datatable_filter"] = true;
        options.params[filter_column] = filter_value;
      }

      this.$axios.get(url, options).then(({ data }) => {
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
    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`${this.endpoint}/` + item.id)
          .then(({ data }) => {
            const index = this.data.indexOf(item);
            this.data.splice(index, 1);
            this.snackbar = data.status;
            this.response = data.message;
            this.getDataFromApi();
          })
          .catch((err) => console.log(err));
    },
  },
};
</script>
