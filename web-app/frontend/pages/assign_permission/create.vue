<template>
  <div v-if="can(`assign_permission_accesss`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ msg }}
      </v-snackbar>
    </div>
    <!-- v-if="permissions && permissions.length > 0" -->
    <Back color="primary" />

    <v-card elevation="0" class="mt-2">
      <v-form ref="form" lazy-validation>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col>
                <div class="display-1 pa-2">Assign Permissions</div>
              </v-col>
              <v-col cols="12">
                <v-row>
                  <v-col md="4">
                    <v-select
                      :rules="Rules"
                      v-model="role_id"
                      :items="roles"
                      item-value="id"
                      item-text="name"
                      placeholder="Role*"
                      outlined
                      dense
                    ></v-select>
                    <span v-if="errors && errors.role_id" class="red--text">
                      {{ errors.role_id[0] }}
                    </span>
                  </v-col>
                </v-row>

                <!-- <v-text-field
                  @input="searchIt"
                  v-model="search"
                  label="Search"
                  single-line
                  hide-details
                ></v-text-field> -->
                <!-- <v-checkbox v-for="(pa, idx) in permissions" :key="idx" :value="pa.id" v-model="permission_ids"
                  :label="`${pa.name}`">
                </v-checkbox> -->
                <!-- <v-checkbox
                  @change="setAllIds"
                  :label="`Select All`"
                  v-model="just_ids"
                >
                </v-checkbox> -->
                <table class="mb-15">
                  <tr style="text-align: center">
                    <th
                      style="
                        width: 600px;
                        text-align: center;
                        padding: 5px 0 !important;
                      "
                    >
                      Module
                    </th>
                    <th style="text-align: center">Access</th>
                    <th style="text-align: center">View</th>
                    <th style="text-align: center">Create</th>
                    <th style="text-align: center">Edit</th>
                    <th style="text-align: center">Delete</th>
                  </tr>
                  <tr v-for="(items, idx) in permissions" :key="idx">
                    <th class="ps-3">{{ capsTitle(idx) }}</th>
                    <th
                      v-for="(pa, idx) in items"
                      :key="idx"
                      style="text-align: center !important"
                      class=""
                    >
                      <v-checkbox
                        :value="pa.id"
                        v-model="permission_ids"
                        :hide-details="true"
                        class="pt-0 py-1 chk-align"
                      >
                      </v-checkbox>
                    </th>
                  </tr>
                </table>

                <!-- <v-row class="mt-5">
                  <v-col
                    xs="12"
                    sm="12"
                    md="3"
                    cols="12"
                    v-for="(items, idx) in permissions"
                    :key="idx"
                  >
                    <div class="componentWrapper my-3">
                      <div class="header">
                        {{ capsTitle(idx) }}
                      </div>
                      <v-checkbox
                        v-for="(pa, idx) in items"
                        :key="idx"
                        :value="pa.id"
                        v-model="permission_ids"
                        :label="capsTitle(pa.name)"
                        :hide-details="true"
                      >
                      </v-checkbox>
                    </div>
                  </v-col>
                </v-row> -->
              </v-col>
              <v-col cols="12">
                <span v-if="errors && errors.permission_ids" class="red--text">
                  {{ errors.permission_ids[0] }}
                </span>
              </v-col>
            </v-row>
          </v-container>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              v-if="can(`assign_permission_create`)"
              dark
              small
              color="primary"
              class="mr-4"
              @click="save"
            >
              Submit
            </v-btn>
          </v-card-actions>
        </v-card-text>
      </v-form>

      <template v-slot:item.action="{ item }">
        <v-icon small class="mr-2" @click="editItem(item)"> mdi-pencil </v-icon>
        <v-icon small @click="deleteItem(item)"> mdi-delete </v-icon>
      </template>
    </v-card>
  </div>
  <NoAccess v-else />
</template>

<script>
import Back from "../../components/Snippets/Back.vue";

export default {
  components: { Back },

  data: () => ({
    role_id: "",
    search: "",
    permission_ids: [],
    permissions: [],
    msg: "",
    snackbar: false,
    Rules: [(v) => !!v || "This field is required"],
    errors: [],
    roles: [],
    just_ids: false,
  }),
  created() {
    let options = {
      params: {
        company_id: this.$auth.user.company_id,
      },
    };

    this.$axios
      .get("assign-permission/nars", options)
      .then(({ data }) => (this.roles = data))
      .catch((err) => console.log(err));

    this.getPermissions();
  },
  methods: {
    capsTitle(val) {
      let res = val;
      let r = res.replace(/[^a-z]/g, " ");
      let title = r.replace(/\b\w/g, (c) => c.toUpperCase());
      return title;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },
    
    getPermissions(url = "dropDownList") {
      this.$axios
        .get(url)
        .then(({ data }) => {
          this.permissions = data.data;
        })
        .catch((err) => console.log(err));
    },
    setAllIds() {
      this.permission_ids = this.just_ids
        ? this.permissions.map((e) => e.id)
        : [];
    },
    searchIt(key) {
      if (key.length == 0) {
        this.getPermissions();
      } else if (key.length > 2) {
        this.getPermissions(`permission/search/${key}`);
      }
      this.permission_ids = [];
    },
    save() {
      this.errors = [];
      let payload = {
        role_id: this.role_id,
        permission_ids: this.permission_ids,
        company_id: this.$auth.user.company_id,
      };

      this.$axios.post("assign-permission", payload).then(({ data }) => {
        if (!data.status) {
          this.errors = data.errors;
          return;
        }
        this.msg = "Permissions has been assigned";
        this.snackbar = true;
        setTimeout(() => this.$router.push("/assign_permission"), 2000);
      });
    },
  },
};
</script>

<style scoped>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

tr:nth-child(even) {
  background-color: #e9e9e9;
}

th,
td {
  border: 1px solid #dddddd;
  /* text-align: center; */
  padding: 5px 5px;
}

.chk-align {
  text-align: center !important;
  margin-top: 8px !important;
  margin-left: 98px !important;
}

* {
  box-sizing: border-box;
}

body > div {
  min-height: 100vh;
  display: flex;
  font-family: "Roboto", sans-serif;
}

.table_responsive {
  max-width: 900px;
  border: 1px solid #00bcd4;
  background-color: #efefef33;
  padding: 15px;
  overflow: auto;
  margin: auto;
  border-radius: 4px;
}

table {
  width: 100%;
  font-size: 13px;
  color: #444;
  white-space: nowrap;
  border-collapse: collapse;
}

table > thead {
  background-color: #00bcd4;
  color: #fff;
}

table > thead th {
  padding: 15px;
}

table th,
table td {
  border: 1px solid #00000017;
  padding: 10px 15px;
}

table > tbody > tr > td > img {
  display: inline-block;
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 50%;
  border: 4px solid #fff;
  box-shadow: 0 2px 6px #0003;
}

.action_btn {
  display: flex;
  justify-content: center;
  gap: 10px;
}

.action_btn > a {
  text-decoration: none;
  color: #444;
  background: #fff;
  border: 1px solid;
  display: inline-block;
  padding: 7px 20px;
  font-weight: bold;
  border-radius: 3px;
  transition: 0.3s ease-in-out;
}

.action_btn > a:nth-child(1) {
  border-color: #26a69a;
}

.action_btn > a:nth-child(2) {
  border-color: orange;
}

.action_btn > a:hover {
  box-shadow: 0 3px 8px #0003;
}

table > tbody > tr {
  background-color: #fff;
  transition: 0.3s ease-in-out;
}

table > tbody > tr:nth-child(even) {
  background-color: rgb(238, 238, 238);
}

table > tbody > tr:hover {
  filter: drop-shadow(0px 2px 6px #0002);
}
</style>
