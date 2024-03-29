<template>
  <div>
    <v-toolbar class="mb-2" dense flat>
      <v-toolbar-title style="color: black"
        ><span> Payslips</span></v-toolbar-title
      >

      <v-btn
        dense
        class="ma-0 px-0"
        x-small
        :ripple="false"
        text
        title="Reload"
      >
        <v-icon class="ml-2" @click="clearFilters" dark>mdi mdi-reload</v-icon>
      </v-btn>

      <v-btn
        v-show="can(`payroll_payslip_generate_access`)"
        dense
        class="ma-0 px-0"
        x-small
        :ripple="false"
        text
        title="Filter By Department"
      >
        <v-icon class="ml-2" @click="dialogVisible = true" dark
          >mdi-filter-multiple
        </v-icon>
      </v-btn>

      <v-btn
        dense
        class="ma-0 px-0"
        x-small
        :ripple="false"
        text
        title="Download All Payslips"
      >
        <v-icon class="ml-2" @click="toggleFilter" dark>mdi-filter </v-icon>
      </v-btn>

      <v-btn
        v-btn
        v-show="selectedItems.length && can(`payroll_payslip_generate_access`)"
        @click="generateNewpayslipsSelected"
        small
        class="primary toolbar-button-design1"
        color="primary"
        title="Payslips For selected"
      >
        Payslips For selected
      </v-btn>

      <v-col cols="4" class="text-right">
        <v-btn
          v-show="can(`payroll_payslip_generate_access`)"
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          text
          title="Generate Payslips by Department"
        >
          <v-icon @click="openPayslipDialog" class="mx-1 white--text"
            >mdi-briefcase-outline</v-icon
          >
        </v-btn>

        <v-btn
          dense
          class="ma-0 px-0"
          x-small
          :ripple="false"
          text
          v-show="downloadAllDisplayStatus"
          download
          :href="payslipsDownloadAllURL"
          small
          title="Download All Payslips"
        >
          <v-icon @click="showFilters = !showFilters" class="mx-1 white--text"
            >mdi mdi-download</v-icon
          >
        </v-btn>
      </v-col>
    </v-toolbar>
  </div>
</template>
<script>
export default {
  data: () => ({
    branchesList: [],
    filterLoader: false,
    filters: {},
    isFilter: false,
    totalRowsCount: 0,
    //server_datatable_totalItems: 1000,
    dialogVisible: false,
    server_datatable_totalItems: 1000,
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    downloadAllDisplayStatus: true,
    dialogPayslipsResults: false,
    payslipsResultsmessages: [],
    payslipsDownloadAllEmployeeidsArray: [],
    payslipsDownloadAllURL: "",
    items: [],
    dataYears: [],
    tab: null,
    pagination: {
      current: 1,
      total: 0,
      per_page: 100,
    },
    options: {},
    Model: "Payroll",
    endpoint: "employee",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    loading: false,
    total: 0,
    paymentMethod: ["Bank Transfer", "Cash", "Cheque"],
    Allowance: [
      "Transport",
      "Travel",
      "Entertainment",
      "Housing",
      "Uniform",
      "Uniform",
      "Medical/health",
    ],
    headers: [
      { text: "#" },
      { text: "E.ID" },
      { text: "Name" },
      { text: "Month/Year" },
      { text: "Designation" },
      { text: "Department" },
      { text: "Basic Salary" },
      { text: "Net Salary" },
      { text: "Payslip" },
      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    headers_table: [
      {
        text: "Emp ID",
        align: "left",
        sortable: true,
        key: "employee_id",
        filterable: true,
        value: "employee_id",
      },
      {
        text: "Name",
        align: "left",
        sortable: true,
        filterable: true,
        key: "display_name",
        value: "first_name",
        filterSpecial: false,
      },

      {
        text: "Department",
        align: "left",
        sortable: true,
        filterable: true,
        key: "department_id",
        value: "department.name.id", //template name should be match
        filterSpecial: true,
      },

      {
        text: "Month/Year",
        align: "left",
        sortable: false,
        key: "year_month",
        filterable: false,
        value: "year_month",
        filterSpecial: false,
      },
      {
        text: "Basic Salary",
        align: "left",
        sortable: true,
        filterable: true,
        key: "payrollbasic",
        value: "payroll.basic_salary",
        filterSpecial: false,
      },
      {
        text: "Net Salary",
        align: "left",
        sortable: true,
        filterable: true,
        key: "net_salary",
        value: "payroll.net_salary",
      },
      {
        text: "Payslip",
        align: "left",
        sortable: false,
        filterable: false,
        key: "payslip",
        value: "payslip",
        filterSpecial: false,
      },
      {
        text: "Actions",
        align: "left",
        sortable: false,
        filterable: false,
        key: "actions",
        value: "actions",
      },
    ],
    datatable_search_textbox: "",
    editedIndex: -1,
    editedItem: { name: "" },
    defaultItem: { name: "" },
    response: "",
    data: [],
    errors: [],
    departments: [],
    department_id: "",
    department_idPopup: "",
    work: {
      first_name: "",
      last_name: "",
      department: "",
      sub_department: "",
      designation: "",
      role: "",
      employee_id: "",
      system_user_id: "",
      user: "",
      profile_picture: "",
      phone_number: "",
      whatsapp_number: "",
      joining_date: "",
    },
    BankInfo: {
      bank_name: "",
      account_no: "",
      account_title: "",
      iban: "",
      address: "",
      remark: "",
      company_id: "",
      employee_id: "",
    },
    salary: {
      basic_salary: "",
      payment_method: "",
      remark: "",
    },
    allowance: {
      name: "",
      amount: "",
      remark: "",
    },
    generatePayslipDialog: false,
    payslip_year: new Date().getFullYear(),
    payslip_month: new Date().getMonth(),
    payslip_year_Popup: new Date().getFullYear(),
    payslip_month_Popup: new Date().getMonth(),

    selectedItems: [],
    allSelected: false,
    monthNames: [
      { value: 1, label: "January" },
      { value: 2, label: "February" },
      { value: 3, label: "March" },
      { value: 4, label: "April" },
      { value: 5, label: "May" },
      { value: 6, label: "June" },
      { value: 7, label: "July" },
      { value: 8, label: "August" },
      { value: 9, label: "September" },
      { value: 10, label: "October" },
      { value: 11, label: "November" },
      { value: 12, label: "December" },
    ],
  }),
};
</script>
