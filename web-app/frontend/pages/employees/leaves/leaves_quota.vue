<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-card class=" ">
      <v-toolbar class="rounded-md" color="popup_background" dense flat>
        <v-col cols="12">
          <v-toolbar-title
            ><span>Leave Group : {{ leave_group_name }}</span></v-toolbar-title
          >
        </v-col>
      </v-toolbar>

      <v-card-text>
        <v-data-table
          v-model="ids"
          item-key="id"
          :headers="headersGroupInfo"
          :items="DialogLeaveGroupData"
          :loading="loading"
          :hide-default-footer="true"
          class="elevation-0"
        >
          <template v-slot:item.leave_type="{ item }" center>
            {{ item.leave_type.name }} ({{ item.leave_type.short_name }})
          </template>
          <template v-slot:item.total="{ item }">
            <v-chip color="black" text-color="white">
              {{ item.leave_type_count }}</v-chip
            >
          </template>
          <template v-slot:item.approved="{ item }">
            <v-chip color="primary"> {{ item.employee_used }}</v-chip>
          </template>
          <template v-slot:item.available="{ item }">
            <v-chip class="ma-2" color="green" text-color="white">
              {{ item.leave_type_count - item.employee_used }}</v-chip
            >
          </template>
        </v-data-table>
      </v-card-text>
    </v-card>
    <!-- <v-row>
        <v-col md="12" class="float-right">
          <div class="float-right">
            <v-pagination v-model="pagination.current" :length="pagination.total" @input="onPageChange"
              :total-visible="12"></v-pagination>
          </div>
        </v-col>
      </v-row> -->
  </div>
</template>
<script>
export default {
  // props: ["table_id", "employee_id", "system_user_id"],
  data: () => ({
    ids: [],
    schdulesList: [],
    employees: [],
    cumulativeIndex: 1,
    perPage: 10,
    currentPage: 1,
    branchesList: [],
    branch_id: null,

    totalRowsCount: 0,

    snack: false,
    snackColor: "",
    snackText: "",
    displayNoRecords: false,

    Module: "Employee Schedule",
    shift_types: [],
    manual_shift: {},
    options: { perPage: 10 },
    options_dialog: {},
    endpoint: "scheduled_employees_index",
    endpoint_dialog: "scheduled_employees_list",
    search: "",
    shifts_for_filter: [],
    dialog_search: "",
    snackbar: false,
    dialog: false,
    editDialog: false,

    loading: false,
    loading_dialog: false,
    isEdit: false,
    total: 0,
    total_dialog: 0,
    system_user_id: 0,

    headersGroupInfo: [
      {
        text: "Leave Type",
        align: "left",
        key: "name",
        sortable: false,
        value: "leave_type",
      },
      {
        text: "Total",
        align: "center",
        key: "name",
        sortable: false,
        value: "total",
      },
      {
        text: "Approved",
        align: "center",
        key: "name",
        sortable: false,
        value: "approved",
      },
      {
        text: "Available",
        align: "center",
        key: "name",
        sortable: false,
        value: "available",
      },
    ],
    pagination: {
      current: 1,
      total: 0,
      per_page: 10,
    },
    response: "",
    data: [],

    errors: [],
    headers_ids: [],
    leave_group_name: "",
    DialogLeaveGroupData: [],
    leaveGroupId: "",
  }),

  computed: {},

  created() {
    this.system_user_id = this.$auth.user.employee.system_user_id;
    this.getDataFromApi();
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

    ScheduleItem(item, type) {
      this.empId = item.employee_id;
      let id = item.employee_id;
      let options = {
        company_id: this.$auth.user.company_id,
      };
      this.$axios
        .get(`get_roster_by_employee/${id}`, { params: options })
        .then(({ data }) => {
          type == "edit" ? (this.isEdit = true) : (this.isEdit = false);
          this.schedules_temp_list = data;
          this.editDialog = true;
        });
    },

    can(per) {
      return this.$pagePermission.can(per, this);
    },
    can_old(per) {
      let { permissions } = this.$auth.user;
      return permissions.includes(per);
    },

    //main

    gotoGroupDetails(leaveGroupId, employee_id, employee_name) {
      if (!leaveGroupId) {
        return false;
      }
      this.dialogLeaveGroup = true;
      this.viewEmployeeName = employee_name;
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          employee_id: employee_id,
        },
      };
      this.$axios
        .get("leave_groups/" + leaveGroupId, options)
        .then(({ data }) => {
          this.DialogLeaveGroupData = data[0].leave_count;
          this.leave_group_name = data[0].group_name;
        });
    },

    getDataFromApi() {
      let options = {
        params: {
          id: this.$auth.user.employee.id,
          company_id: this.$auth.user.company_id,
          // department_id: this.department_filter_id,
        },
      };

      this.$axios.get("employee", options).then(({ data }) => {
        if (data.data[0]) this.employeeObject = data.data[0];
        if (this.employeeObject && this.employeeObject.leave_group_id)
          this.gotoGroupDetails(
            this.employeeObject.leave_group_id,
            this.employeeObject.employee_id,
            this.employeeObject.first_name
          );
      });
    },
  },
};
</script>
