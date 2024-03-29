<template>
  <v-container>
    <component v-if="isEmployee" :is="employeeComponent" />
    <template v-else>
      <component :is="defaultCardComponent" :page="id" />
      <component :is="defaultComponent" :page="id" />
    </template>
  </v-container>
</template>

<script>
import Dashboard from "../../components/employee/Dashboard.vue";
import DashboardCardComponent from "../../components/widgets/DashboardCardComponent.vue";
import DashboardComponent from "../../components/widgets/DashboardComponent.vue";

export default {
  components: {
    Dashboard,
    DashboardCardComponent,
    DashboardComponent,
  },
  computed: {
    id() {
      return "dashboard" + 2;
    },
    isEmployee() {
      return this.$store.state.loginType === "employee";
    },
    employeeComponent() {
      return Dashboard;
    },
    defaultCardComponent() {
      return DashboardCardComponent;
    },
    defaultComponent() {
      return DashboardComponent;
    },
  },

  created() {
    //this.$router.push("/employees/profile");
  },
};
</script>
