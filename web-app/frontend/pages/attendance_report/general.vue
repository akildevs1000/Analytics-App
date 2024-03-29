<template>
  <AttendanceReport
    title="General Reports"
    shift_type_id="1"
    :headers="headers"
    process_file_endpoint=""
    render_endpoint="render_general_report"
  />
</template>

<script>
import AttendanceReport from "../../components/attendance_report/report.vue";

export default {
  components: { AttendanceReport },

  data: () => ({
    headers: [
      {
        text: "Date",
        align: "left",
        sortable: true,
        filterable: false,
        value: "date",
      },
      {
        text: "Emp.ID",
        align: "left",
        sortable: true,
        filterable: true,
        value: "employee_id",
      },
      {
        text: "Employee",
        align: "left",
        sortable: false,
        filterable: true,
        value: "employee_name",
        key: "employee_name",
      },
      {
        text: "Department",
        align: "left",
        sortable: false,
        filterable: true,
        value: "employee.department.name",
      },
      {
        text: "Shift",
        align: "left",
        sortable: false,
        filterable: true,
        value: "shift",
      },
      {
        text: "Status",
        align: "left",
        sortable: true,
        filterable: false,
        value: "status",
      },
      {
        text: "In",
        align: "left",
        sortable: true,
        filterable: true,
        value: "in",
      },
      {
        text: "Out",
        align: "left",
        sortable: true,
        filterable: true,
        value: "out",
      },
      {
        text: "Total Hrs",
        align: "left",
        sortable: true,
        filterable: true,
        value: "total_hrs",
      },
      {
        text: "OT",
        align: "left",
        sortable: true,
        filterable: true,
        value: "ot",
      },
      // {
      //   text: "Late coming",
      //   align: "left",
      //   sortable: false,
      //   value: "late_coming"
      // },
      // {
      //   text: "Early Going",
      //   align: "left",
      //   sortable: false,
      //   value: "early_going"
      // },
      // {
      //   text: "D.In",
      //   align: "left",
      //   sortable: false,
      //   value: "device_in"
      // },
      // {
      //   text: "D.Out",
      //   align: "left",
      //   sortable: false,
      //   value: "device_out"
      // },

      { text: "Actions", value: "actions", sortable: false },
    ],
  }),
};
</script>
