<template>
  <div class="mt-8">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-container>
      <v-btn v-if="can(`employee_document_edit_access`)" @click="addDocumentInfo" class="primary mb-2">Add Document +
      </v-btn>
      <v-row v-for="(d, index) in Document.items" :key="index">
        <v-col cols="2">
          <label class="col-form-label">Title <span class="text-danger">*</span></label>
          <v-text-field dense outlined v-model="d.title"></v-text-field>
          <span v-if="errors && errors.title" class="text-danger mt-2">{{
            errors.title[0]
          }}</span>
        </v-col>
        <v-col cols="2">
          <label class="col-form-label">File <span class="text-danger">*</span></label>
          <v-file-input dense outlined v-model="d.file">
            <template v-slot:selection="{ text }">
              <v-chip v-if="text" small label color="primary" class="ma-1">
                {{ text }}
              </v-chip>
            </template>
          </v-file-input>

          <span v-if="errors && errors.value" class="text-danger mt-5">{{
            errors.value[0]
          }}</span>
        </v-col>
        <v-col cols="2">
          <v-icon class="error--text mt-10" @click="removeItem(index)">mdi-delete</v-icon>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12">
          <v-btn :disabled="!Document.items.length" class="primary" small @click="save_document_info">Save</v-btn>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="6">
          <table style="border-collapse: collapse; width: 100%">
            <thead>
              <tr>
                <th style="
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                  ">
                  Title
                </th>
                <th style="
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                  ">
                  File
                </th>
                <th style="
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                  ">
                  Action
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(d, index) in document_list" :key="index">
                <td style="
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                  ">
                  {{ d.key }}
                </td>
                <td style="
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                  ">
                  <a :href="d.value" target="_blank">
                    <v-btn small class="primary">
                      open file <v-icon>mdi-open-window</v-icon>
                    </v-btn>
                  </a>
                </td>
                <td style="
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                  ">
                  <v-icon color="error" @click="delete_document(d.id)">
                    mdi-delete
                  </v-icon>
                </td>
              </tr>
              <!-- Add more rows as needed -->
            </tbody>
          </table>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  props: ["employeeId"],
  data() {
    return {
      snackbar: false,
      valid: true,
      documents: false,
      response: "",
      errors: [],
      FileRules: [
        (value) =>
          !value ||
          value.size < 200000 ||
          "File size should be less than 200 KB!",
      ],
      TitleRules: [(v) => !!v || "Title is required"],
      Document: {
        items: [{ title: "", file: "" }],
      },
      document_list: [],
    };
  },
  created() {
    this.getInfo();
  },
  methods: {
    getInfo() {
      this.$axios
        .get(`document`, {
          params: { company_id: this.$auth?.user?.company?.id },
        })
        .then(({ data }) => {
          this.document_list = data;
          this.loading = false;
        });
    },
    can(item) {
      return true;
    },
    caps(str) {
      if (str == "" || str == null) {
        return "---";
      } else {
        let res = str.toString();
        return res.replace(/\b\w/g, (c) => c.toUpperCase());
      }
    },

    addDocumentInfo() {
      this.Document.items.push({
        title: "",
        file: "",
      });
    },

    save_document_info() {
      let options = {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      };
      let payload = new FormData();

      this.Document.items.forEach((e) => {
        payload.append(`items[][key]`, e.title);
        payload.append(`items[][value]`, e.file || {});
      });

      payload.append(`company_id`, this.$auth?.user?.company?.id);

      this.$axios
        .post(`document`, payload, options)
        .then(({ data }) => {
          this.loading = false;

          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.errors = [];
            this.snackbar = true;
            this.response = data.message;
            this.getInfo();
            this.Document.items = [{ title: "", file: "" }];
            this.close_document_info();
          }
        })
        .catch((e) => console.log(e));
    },

    close_document_info() {
      this.documents = false;
      this.errors = [];
    },

    removeItem(index) {
      this.Document.items.splice(index, 1);
    },

    delete_document(id) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`document/${id}`)
          .then(({ data }) => {
            this.loading = false;

            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.errors = [];
              this.snackbar = true;
              this.response = data.message;
              this.getInfo();
              this.close_document_info();
            }
          })
          .catch((e) => console.log(e));
    },
  },
};
</script>
