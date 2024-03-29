<template>
  <form @submit.prevent="onSubmit">
    <label for="new-todo-input"> Devices List </label>
    <v-row class="mt-5 mb-10">
      <v-col md="6">
        <h3>Devices List</h3>
        <v-select v-model="deviseOptionsselected" :items="deviseslist" label="Select options" item-text="name"
          item-value="id" multiple></v-select>
      </v-col>
      <v-col md="6">
        <h3>Employees List</h3>
        <v-select v-model="employeeOptionsselected" :items="employeesList" label="Select Employees"
          item-text="display_name" item-value="employee_id" multiple></v-select>
      </v-col>
    </v-row>

    <button type="submit">Add</button>
  </form>
</template>

<script>
export default {
  data() {
    return {
      label: "",

      endpointEmployee: "employee",
      endpointDevise: "device",
      search: "",
      snackbar: false,
      dialog: false,
      selected_artists: [5678],
      artists: [
        { text: "Some Artist", value: 1234 },
        { text: "Another Artist", value: 5678 },
      ],
      deviseOptionsselected: [],
      deviseslist: [],
      employeeOptionsselected: [],
      employeesList: [],
      onSubmitarray: [],
    };
  },
  created() {
    this.getDevisesDataFromApi();
    this.getEmployeesDataFromApi();
  },

  methods: {
    onSubmit() {

      //this.$emit("todo-added", this.employeeOptions);
      let options = {
        params: {
          deviseOptionsselected: this.deviseOptionsselected,
          employeeOptionsselected: this.employeeOptionsselected,
        },
      };


      // this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
      //   //this.deviseslist = data.data;
      // });
    },
    getDevisesDataFromApi(url = this.endpointDevise) {
      //this.loading = true;
      // let page = this.pagination.current;
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
        },
      };
      let page = 1;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.deviseslist = data.data;
      });
    },
    getEmployeesDataFromApi(url = this.endpointEmployee) {
      //this.loading = true;
      // let page = this.pagination.current;
      let options = {
        params: {
          per_page: 1000, //this.pagination.per_page,
          company_id: this.$auth.user.company_id,
        },
      };
      let page = 1;
      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        this.employeesList = data.data;
      });
    },
  },
};
</script>
