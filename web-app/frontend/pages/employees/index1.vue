<template>
  <v-card elevation="0" class="mt-2">
    <v-toolbar class="mb-2 white--text" color="white" dense flat>
      <v-toolbar-title>
        <span style="color: black"> {{ Model }}s </span></v-toolbar-title
      >
      <v-btn
        dense
        class="ma-0 px-0"
        x-small
        :ripple="false"
        text
        title="Filter"
      >
        <v-icon @click="toggleFilter" class="mx-1 ml-2">mdi-filter</v-icon>
      </v-btn>
      <v-spacer></v-spacer>
      <!-- <SearchEntity
        :endpoint="endpoint"
        @search="(e) => (data = e)"
        @default="handleSearch"
      /> -->
      <!-- <ImportEntity @success="(e) => getDataFromApi()" />
      <ExportEntity :data="data.data" />
      <CreateEntity @success="(e) => getDataFromApi()" /> -->
    </v-toolbar>
    <v-data-table
      dense
      :headers="headers"
      :items="data.data"
      :options.sync="options"
      :loading="loading"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
      class="elevation-1"
      :server-items-length="data.total"
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
                  header.filterSpecial && header.value == 'branch.branch_name'
                "
                outlined
                dense
                small
                v-model="filters[header.key]"
                item-text="name"
                item-value="id"
                :items="[{ name: `All Branches`, id: `` }, ...branches_list]"
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
                  header.filterSpecial && header.value == 'department_name_id'
                "
                outlined
                dense
                small
                v-model="filters[header.key]"
                item-text="name"
                item-value="id"
                :items="[{ name: `All Departments`, id: `` }, ...departments]"
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
                v-if="header.filterSpecial && header.value == 'timezone.name'"
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
      <template v-slot:item.employee_id="{ item }">
        <strong>{{ item.employee_id }} </strong><br /><span
          style="font-size: 12px"
          >{{ item.system_user_id }}</span
        >
      </template>

      <template v-slot:item.first_name="{ item, index }" style="width: 300px">
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

              {{
                item.user.role && item.user.role.name != "---"
                  ? "(Role:" + caps(item.user.role.name) + ")"
                  : ""
              }}

              <!-- {{
                    item.user.branch_login &&
                    "(" + item.user.branch_login.branch_name + ")"
                  }} -->
            </div>
          </v-col>
        </v-row>
      </template>

      <template v-slot:item.branch.branch_name="{ item }">
        {{ caps(item.branch && item.branch.branch_name) }}
        <div class="secondary-value">
          {{ item.user.branch_login && "(Branch Owner)" }}
        </div>
      </template>
      <template v-slot:item.department_name_id="{ item }">
        <strong>{{ caps(item.department.name) }}</strong>
        <div>{{ caps(item.sub_department.name) }}</div>
      </template>
      <template v-slot:item.phone_number="{ item }">
        {{ item.phone_number }}
      </template>
      <template v-slot:item.user.email="{ item }" style="width: 200px">
        {{ item.user.email }}
      </template>
      <template v-slot:item.timezone.name="{ item }">
        {{ item.timezone ? item.timezone.timezone_name : "" }}
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
              v-if="can('employee_profile_view')"
              @click="viewItem(item)"
            >
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small> mdi-eye </v-icon>
                View
              </v-list-item-title>
            </v-list-item>
            <v-list-item v-if="can('employee_edit')" @click="editItem(item)">
              <v-list-item-title style="cursor: pointer">
                <v-icon color="secondary" small> mdi-pencil </v-icon>
                Edit
              </v-list-item-title>
            </v-list-item>
            <v-list-item
              v-if="can('employee_delete')"
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
</template>

<script>
import CreateEntity from "../../components/Snippets/Employee/Create.vue";
import EditEntity from "../../components/Snippets/Employee/Edit.vue";
import ExportEntity from "../../components/Snippets/Employee/Export.vue";
import ImportEntity from "../../components/Snippets/Employee/Import.vue";
import SearchEntity from "../../components/Snippets/Common/Search.vue";

import EmployeeProfileView from "../../components/EmployeesLogin/EmployeeLanding.vue";

export default {
  components: {
    EmployeeProfileView,
    CreateEntity,
    EditEntity,
    // ExportEntity,
    // ImportEntity,
    // SearchEntity,
  },

  data: () => ({
    Model: "Employee",
    endpoint: "employee",
    loading: false,
    data: [],
    departments: [],
    branches_list: [],
    timezones: [],
    options: {},
    isFilter: false,
    filters: {},
    isCompany: true,
    headers: [
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
        text: "Department",
        align: "left",
        sortable: true,
        key: "department_name_id",
        value: "department_name_id", //template name should be match for sorting sub table should be the same
        width: "200px",
        filterable: true,
        filterSpecial: true,
      },
      {
        text: "Mobile",
        align: "left",
        sortable: true,
        key: "mobile",
        value: "phone_number", // search and sorting enable if value matches with template name
        width: "150px",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Email",
        align: "left",
        sortable: true,
        key: "email",
        value: "user.email",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Timezone",
        align: "left",
        sortable: true,
        key: "timezone_id",
        value: "timezone.name",
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
  }),

  async created() {
    if (this.$auth.user.branch_id) {
      this.isCompany = false;
      return;
    }
    this.headers.splice(2, 0, {
      text: "Branch",
      align: "left",
      sortable: true,
      key: "branch_id",
      value: "branch.branch_name",
      filterable: true,
      filterSpecial: true,
    });

    this.branches_list = await this.$store.dispatch("branches_list");
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
    async handleSearch(e) {
      await this.getDataFromApi();
    },
    async toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;

      if (this.isFilter) {
        if (this.$auth.user.branch_id) {
          this.isCompany = false;
          await this.applyFilters(this.$auth.user.branch_id);
          return;

          // this.branch_id = this.$auth.user.branch_id;
          // this.employee.branch_id = this.$auth.user.branch_id;
          // await this.getDepartments(this.branch_id);
        }

        this.branches_list = await this.$store.dispatch("branches_list");
      }
    },

    async applyFilters(id) {
      await this.getDepartments(id);
      await this.getTimezone(id);
      await this.getDataFromApi();
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
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

    async getDataFromApi() {
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let options = {
        endpoint: this.endpoint,
        isFilter: this.isFilter,
        params: {
          page: page,
          sortBy: sortBy ? sortBy[0] : "",
          sortDesc: sortDesc ? sortDesc[0] : "",
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };

      this.data = await this.$store.dispatch("employees", options);

      this.loading = false;
    },
  },
};
</script>
