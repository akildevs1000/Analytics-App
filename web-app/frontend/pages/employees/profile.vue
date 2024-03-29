<template>
  <div>
    <EmployeeProfileView
      :table_id="id"
      :employee_id="employee_id"
      :system_user_id="system_user_id"
    />
  </div>
</template>
<script>
import EmployeeProfileView from "../../components/EmployeesLogin/EmployeeLanding.vue";
export default {
  components: {
    EmployeeProfileView,
  },
  data: () => ({
    system_user_id: "",

    employee_id: "",
    id: "",
  }),

  created() {
    this.system_user_id = this.$auth.user.employee.system_user_id;

    this.employee_id = this.$auth.user.employee.employee_id;

    this.id = this.$auth.user.employee.id;
  },
  mounted() {
    // this.system_user_id = this.$auth.user.employee.system_user_id;
    // this.employee_id = this.$auth.user.employee.employee_id;
    // this.id = this.$auth.user.employee.id;
  },

  methods: {},
};
</script>
