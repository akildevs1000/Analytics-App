<template>
  <AttendanceReport
    title="Multi In/Out Reports"
    shift_type_id="2"
    :headers="headers"
    process_file_endpoint="multi_in_out_"
    render_endpoint="render_multi_inout_report"
  />
</template>

<script>
import AttendanceReport from "../../../components/attendance_report/report.vue";

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
        key: "date",
      },
      {
        text: "Emp.ID",
        align: "left",
        sortable: true,
        filterable: true,
        value: "employee_id",
        key: "employee_id",
      },
      {
        text: "Name",
        align: "left",
        sortable: false,
        filterable: true,
        value: "employee_first_name",
        key: "item.employee",
      },
      {
        text: "Department",
        align: "left",
        sortable: false,
        filterable: true,
        value: "employee.department.name",
      },
      {
        text: "In1",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in1",
      },
      {
        text: "Out1",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out1",
      },
      {
        text: "In2",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in2",
      },
      {
        text: "Out2",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out2",
      },
      {
        text: "In3",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in3",
      },
      {
        text: "Out3",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out3",
      },
      {
        text: "In4",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in4",
      },
      {
        text: "Out4",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out4",
      },
      {
        text: "In5",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in5",
      },
      {
        text: "Out5",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out5",
      },
      {
        text: "In6",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in6",
      },
      {
        text: "Out6",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out6",
      },
      {
        text: "In7",
        align: "left",
        sortable: true,
        filterable: false,
        value: "in7",
      },
      {
        text: "Out7",
        align: "left",
        sortable: true,
        filterable: false,
        value: "out7",
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
      {
        text: "Status",
        align: "left",
        sortable: true,
        filterable: false,
        value: "status",
      },
      { text: "Actions", value: "actions", filterable: false, sortable: false },
    ],
  }),
};
</script>
