<template>
  <div>
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>

    <v-dialog persistent v-model="dialogLeaveGroup" width="600px">
      <v-card>
        <v-card-title dense class="primary white--text background">
          Statistics
          <v-spacer></v-spacer>
          <v-icon @click="dialogLeaveGroup = false" outlined dark color="white">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-data-table
            v-model="ids"
            item-key="id"
            :headers="headersGroupInfo"
            :items="DialogLeavesList"
            :hide-default-footer="true"
            class="elevation-1"
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
    </v-dialog>
    <v-dialog persistent v-model="dialog" width="600px">
      <v-card>
        <v-card-title dense class="popup_background">
          <span class="headline" v-if="editedIndex == -1">New Leave </span>
          <span class="headline" v-else>Edit </span>
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark color="black">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <label
                  style="padding-bottom: 5px"
                  v-if="leave_available_count != ''"
                  >Available Leaves :
                  <v-chip color="primary">{{
                    leave_available_count
                  }}</v-chip></label
                >
              </v-col>

              <v-col md="12" sm="12" cols="12">
                <!-- <label for="" style="padding-bottom: 5px"
                  >Select leave Type</label
                > -->
                <v-select
                  label="Select Leave Type"
                  @change="verifyAvailableCount"
                  :items="leaveTypes"
                  item-text="leave_type.name"
                  item-value="leave_type.id"
                  placeholder="Select Leave Type"
                  v-model="editedItem.leave_type_id"
                  :hide-details="!errors.leave_type_id"
                  :error="errors.leave_type_id"
                  :error-messages="
                    errors && errors.leave_type_id
                      ? errors.leave_type_id[0]
                      : ''
                  "
                  dense
                  outlined
                ></v-select>
              </v-col>
              <v-col md="12" sm="12" cols="12">
                <v-menu
                  ref="from_menu"
                  v-model="start_menu"
                  :close-on-content-click="false"
                  :return-value.sync="editedItem.start_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <!-- <label for="" style="padding-bottom: 5px">From Date</label> -->
                    <v-text-field
                      label="From Date"
                      style="height: 45px"
                      outlined
                      dense
                      v-model="editedItem.start_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      :error-messages="
                        errors && errors.start_date ? errors.start_date[0] : ''
                      "
                    >
                    </v-text-field>
                  </template>
                  <v-date-picker
                    v-model="editedItem.start_date"
                    small
                    no-title
                    scrollable
                    :min="todayDate"
                    @change="update_EdititemStart"
                  >
                  </v-date-picker>
                </v-menu>
              </v-col>

              <v-col md="12" sm="12" cols="12">
                <v-menu
                  ref="end_menu"
                  v-model="end_menu"
                  :close-on-content-click="false"
                  :return-value.sync="editedItem.end_date"
                  transition="scale-transition"
                  offset-y
                  min-width="auto"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <!-- <div class="mb-1">End Date</div> -->
                    <v-text-field
                      label="To Date"
                      style="height: 45px"
                      outlined
                      dense
                      v-model="editedItem.end_date"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                      :error-messages="
                        errors && errors.end_date ? errors.end_date[0] : ''
                      "
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    small
                    v-model="editedItem.end_date"
                    @change="update_EdititemEnd"
                    :min="editedItem.start_date"
                    no-title
                    scrollable
                  >
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col md="12" sm="12" cols="12">
                <!-- <label for="" style="padding-bottom: 5px">Reason</label> -->
                <v-text-field
                  label="Reason"
                  dense
                  outlined
                  v-model="editedItem.reason"
                  placeholder="Reason/Notes"
                  :error-messages="
                    errors && errors.reason ? errors.reason[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <v-col cols="12" v-if="errors && errors.reporting_manager_id">
                <label for="" style="padding-bottom: 5px; color: red"
                  >Reporting Manager ID is not assigned. Contact Admin
                </label>
              </v-col>
              <v-col cols="12">
                <label v-if="editedIndex != -1"
                  ><strong> Uploaded Documents </strong> ({{
                    document_list.length
                  }})</label
                >

                <v-btn
                  title="Maximum file upload size is 100Kb"
                  cols="2"
                  @click="openDialogUploadDocuments"
                  small
                  dense
                  class="primary mb-2"
                  style="float: right"
                  >Add Document+
                </v-btn>
              </v-col>
              <v-col
                cols="12"
                v-if="document_list.length && editedItem.status == 0"
              >
                <table style="border-collapse: collapse; width: 100%">
                  <thead>
                    <tr>
                      <th
                        style="
                          border: 1px solid #dddddd;
                          text-align: left;
                          padding: 8px;
                        "
                      >
                        Title
                      </th>
                      <th
                        style="
                          border: 1px solid #dddddd;
                          text-align: left;
                          padding: 8px;
                        "
                      >
                        File
                      </th>
                      <th
                        style="
                          border: 1px solid #dddddd;
                          text-align: left;
                          padding: 8px;
                        "
                      >
                        Action
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(d, index) in document_list" :key="index">
                      <td
                        style="
                          border: 1px solid #dddddd;
                          text-align: left;
                          padding: 8px;
                        "
                      >
                        {{ d.key }}
                      </td>
                      <td
                        style="
                          border: 1px solid #dddddd;
                          text-align: center;
                          padding: 8px;
                        "
                      >
                        <a :href="d.value" target="_blank">
                          <v-btn small class=" ">
                            View file <v-icon>mdi-open-window</v-icon>
                          </v-btn>
                        </a>
                      </td>
                      <td
                        style="
                          border: 1px solid #dddddd;
                          text-align: left;
                          padding: 8px;
                        "
                      >
                        <v-icon
                          color="error"
                          @click="delete_document(d.id, d.leave_id)"
                        >
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
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn v-if="newLeaveApplication" class="primary" small @click="save"
            >Save</v-btn
          >
          <v-btn v-else class="danger" small>Reached maximum Leave count</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog
      persistent
      v-model="dialogUploadDocuments"
      width="800px"
      height="400px"
    >
      <v-card>
        <v-card-title dense class="popup_background">
          Documents
          <v-spacer></v-spacer>
          <v-icon
            @click="dialogUploadDocuments = false"
            outlined
            dark
            color="black"
          >
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" style="min-height: 200px" class="mt-4">
                <v-row v-for="(d, index) in Document.items" :key="index">
                  <v-col cols="5" style="padding: 0px">
                    <v-text-field
                      label="Title"
                      small
                      dense
                      outlined
                      v-model="d.title"
                      placeholder="Title"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6" class="file-upload" style="padding: 0px">
                    <v-file-input
                      @change="uploadFilesizeValidation($event, index)"
                      small
                      dense
                      outlined
                      v-model="d.file"
                      style="padding: 0px; margin: 0px"
                    >
                      <template
                        v-slot:selection="{ text }"
                        style="padding: 0px; margin: 0px"
                      >
                        <v-chip
                          v-if="text"
                          small
                          label
                          class="ma-1 text-truncate custom-chip1"
                          style="width: 85px; padding: 0px; margin: 0px"
                          overflow="hidden"
                        >
                          {{ text }}
                        </v-chip>
                      </template>
                    </v-file-input>

                    <span
                      v-if="
                        errorsFileUpload[index] && errorsFileUpload[index].value
                      "
                      class="text-danger mt-5"
                      >{{ errorsFileUpload[index].value[0] }}</span
                    >
                  </v-col>
                  <v-col cols="1" style="padding: 0px">
                    <v-icon class="error--text mt-1" @click="removeItem(index)"
                      >mdi-delete</v-icon
                    >
                  </v-col>
                </v-row>
                <v-row>
                  <v-col cols="12" class="text-right" style="padding: 0px">
                    <!-- <v-btn
                      title="Maximum file upload size is 100Kb"
                      cols="2"
                      @click="addDocumentInfo"
                      small
                      dense
                      class="primary"
                      style="float: right"
                      >Add +
                    </v-btn> -->
                    <v-icon
                      style="float: right"
                      class="black--text mt-1 text-end"
                      @click="addDocumentInfo"
                      >mdi-plus-circle</v-icon
                    >
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn class="error" small @click="closeDialogUploadDocuments">
            Cancel
          </v-btn>
          <v-spacer></v-spacer>

          <v-btn class="primary" small @click="SaveDocumentsDialog">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog persistent v-model="dialogView" width="1000px">
      <v-card>
        <v-card-title dense class="popup_background">
          Leave Information
          <v-spacer></v-spacer>
          <v-icon @click="dialogView = false" outlined dark color="black">
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-row>
            <v-col cols="12">
              <v-row>
                <v-col cols="4">
                  <label for="">
                    <strong> Name</strong>
                  </label>
                </v-col>
                <v-col cols="8">
                  <label for="">: {{ dialogViewObject.employee_name }}</label>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <strong>Group </strong>
                </v-col>
                <v-col cols="8">
                  <label for=""
                    >: {{ dialogViewObject.leave_group_name }}</label
                  >
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <strong> Type</strong>
                </v-col>
                <v-col cols="8">
                  <label for="">: {{ dialogViewObject.leave_type }}</label>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <strong>From </strong>
                </v-col>
                <v-col cols="8">
                  <label for="">: {{ dialogViewObject.from_date }}</label>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <strong>To </strong>
                </v-col>
                <v-col cols="8">
                  <label for="">: {{ dialogViewObject.to_date }}</label>
                </v-col>
              </v-row>
            </v-col>
            <v-col col="7">
              <v-row>
                <v-col cols="4">
                  <strong>Applied on </strong>
                </v-col>
                <v-col cols="8">
                  <label for="">: {{ dialogViewObject.applied_date }}</label>
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <strong> Manager </strong>
                </v-col>
                <v-col cols="8">
                  <label for=""
                    >: {{ dialogViewObject.reporting_manager }}</label
                  >
                </v-col>
              </v-row>
              <v-row>
                <v-col cols="4">
                  <strong>Status </strong>
                </v-col>
                <v-col cols="8">
                  <label for=""
                    >:
                    <v-chip
                      v-if="dialogViewObject.status == 1"
                      small
                      class="p-2 mx-1"
                      color="primary"
                    >
                      Approved
                    </v-chip>
                    <v-chip
                      v-if="dialogViewObject.status == 2"
                      small
                      class="p-2 mx-1"
                      color="error"
                    >
                      Rejected
                    </v-chip>
                    <v-chip
                      v-if="dialogViewObject.status == 0"
                      small
                      class="p-2 mx-1"
                      color="secondary"
                    >
                      Pending
                    </v-chip></label
                  >
                </v-col>
              </v-row>
              <v-row v-if="dialogViewObject.status == 1">
                <v-col cols="4">
                  <strong>Approved Date </strong>
                </v-col>
                <v-col cols="8">
                  <label for=""
                    >: {{ dialogViewObject.approved_datetime }}</label
                  >
                </v-col>
              </v-row>
              <v-row v-else-if="dialogViewObject.status == 2">
                <v-col cols="4">
                  <strong>Rejected Date </strong>
                </v-col>
                <v-col cols="8">
                  <label for=""
                    >: {{ dialogViewObject.approved_datetime }}</label
                  >
                </v-col>
              </v-row>

              <v-row v-if="dialogViewObject.status != 0">
                <v-col cols="4">
                  <strong
                    >{{
                      dialogViewObject.status == 1 ? "Approved" : "Rejected"
                    }}
                    Notes
                  </strong>
                </v-col>
                <v-col cols="8">
                  : {{ dialogViewObject.approve_reject_notes }}
                </v-col>
              </v-row>
            </v-col>
            <v-col cols="12">
              <strong> Leave Notes </strong>:
              <label for="">: {{ dialogViewObject.reason }}</label>
            </v-col>
            <label
              ><strong>Uploaded Documents</strong> ({{
                document_list.length
              }})</label
            >
            <v-col cols="12" v-if="document_list.length">
              <table style="border-collapse: collapse; width: 100%">
                <thead>
                  <tr>
                    <th
                      style="
                        border: 1px solid #dddddd;
                        text-align: left;
                        padding: 8px;
                      "
                    >
                      Title
                    </th>
                    <th
                      style="
                        border: 1px solid #dddddd;
                        text-align: center;
                        padding: 8px;
                      "
                    >
                      File
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(d, index) in document_list" :key="index">
                    <td
                      style="
                        border: 1px solid #dddddd;
                        text-align: left;
                        padding: 8px;
                      "
                    >
                      {{ d.key }}
                    </td>
                    <td
                      style="
                        border: 1px solid #dddddd;
                        text-align: center;
                        padding: 8px;
                      "
                    >
                      <a :href="d.value" target="_blank">
                        <v-btn small class="primary">
                          View File <v-icon>mdi-open-window</v-icon>
                        </v-btn>
                      </a>
                    </td>
                  </tr>
                  <!-- Add more rows as needed -->
                </tbody>
              </table>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-row>
      <v-col md="12">
        <v-card class="mb-5">
          <v-toolbar
            class="rounded-md"
            color="popup_background"
            dense
            flat
            style="width: 100%"
          >
            <v-toolbar-title>
              Leave Group:

              {{
                (DialogLeaveGroupInfo[0] &&
                  DialogLeaveGroupInfo[0].group_name) ||
                "---"
              }}
            </v-toolbar-title>

            <v-spacer></v-spacer>
            <v-toolbar-items>
              <!-- <v-col>
                <v-btn
                  v-if="can(`leave_application_create`)"
                  small
                  color="primary"
                  @click="dialogLeaveGroup = true"
                  class="mb-2"
                >
                  count <v-icon>mdi-information</v-icon></v-btn
                >
              </v-col> -->
              <v-col>
                <v-icon
                  :disabled="!$auth.user.employee.leave_group_id"
                  @click="openNewDialog()"
                  class="mb-2"
                  >mdi-plus-circle</v-icon
                >
              </v-col>
            </v-toolbar-items>
          </v-toolbar>

          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>
          <v-data-table
            v-model="ids"
            item-key="id"
            :headers="headers"
            :items="data"
            :loading="loading"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1 alternate-rows"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="header in headers" :key="header.text">
                  <v-text-field
                    clearable
                    :hide-details="true"
                    v-if="header.filterable && header.text != 'Status'"
                    v-model="filters[header.value]"
                    id="header.value"
                    @input="applyFilters(header.value, $event)"
                    outlined
                    dense
                    autocomplete="off"
                  ></v-text-field>

                  <v-select
                    :hide-details="true"
                    @change="applyFilters('status', $event)"
                    item-value="value"
                    item-text="title"
                    v-model="filters[header.value]"
                    outlined
                    dense
                    v-else-if="header.filterable && header.text == 'Status'"
                    :items="[
                      { value: '', title: 'All' },
                      { value: 'approved', title: 'Approved' },
                      {
                        value: 'rejected',
                        title: 'Rejected',
                      },
                      { value: 'pending', title: 'Pending' },
                    ]"
                  ></v-select>
                </td>
              </tr>
            </template>
            <template v-slot:item.name="{ item }">
              {{ item.employee.first_name }} {{ item.employee.last_name }}
            </template>
            <template v-slot:item.group_name="{ item }">
              {{
                item.employee.leave_group &&
                item.employee.leave_group.group_name
              }}
            </template>

            <template v-slot:item.leave_type_name="{ item }">
              {{ item.leave_type.name }}
            </template>
            <template v-slot:item.start_date="{ item }">
              {{ item.start_date }}
            </template>
            <template v-slot:item.end_date="{ item }">
              {{ item.end_date }}
            </template>
            <template v-slot:item.reason="{ item }">
              {{ item.reason.substr(0, 30) + "..." }}
            </template>
            <template v-slot:item.reporting="{ item }">
              {{ item.reporting.first_name }} {{ item.reporting.last_name }}
            </template>
            <template v-slot:item.created_at="{ item }">
              {{ getCurrentDateTime(item.created_at) }}
            </template>
            <template v-slot:item.status="{ item }">
              <v-chip
                v-if="item.status == 1"
                small
                class="p-2 mx-1"
                color="primary"
              >
                Approved
              </v-chip>
              <v-chip
                v-if="item.status == 2"
                small
                class="p-2 mx-1"
                color="error"
              >
                Rejected
              </v-chip>
              <v-chip
                v-if="item.status == 0"
                small
                class="p-2 mx-1"
                color="secondary"
              >
                Pending
              </v-chip>
            </template>

            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <v-list-item @click="view(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small @click="view(item)">
                        {{
                          item.announcement === "customer"
                            ? ""
                            : "mdi-information"
                        }}
                      </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="editItem(item)" v-if="item.status == 0">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="secondary" small @click="editItem(item)">
                        mdi-pencil
                      </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    @click="deleteItem(item)"
                    v-if="item.status == 0"
                  >
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="error" small @click="deleteItem(item)">
                        {{
                          item.announcement === "customer" ? "" : "mdi-delete"
                        }}
                      </v-icon>
                      Delete
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
            <template v-slot:no-data>
              <!-- <v-btn color="primary" @click="initialize">Reset</v-btn> -->
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script>
export default {
  data: () => ({
    dialogUploadDocuments: false,
    valid: true,
    documents: false,
    response: "",
    errors: [],
    errorsFileUpload: [],
    FileRules: [
      (value) =>
        !value ||
        value.size < 200000 ||
        "File size should be less than 200 KB!",
    ],
    TitleRules: [(v) => !!v || "Title is required"],
    Document: {
      items: {
        title: "",
        file: "",
      },
    },
    document_list: [],

    leave_available_count: "",
    newLeaveApplication: true,
    filters: {},
    isFilter: false,
    DialogLeavesList: [],
    DialogLeaveGroupInfo: [],
    dialogLeaveGroup: false,
    attrs: {},
    dialogView: false,
    dialogViewObject: {
      id: "",
      employee_name: "",
      leave_type: "",
      from_date: "",
      to_date: "",
      approved_manager: "",
      status: "",
      reason: "",
      applied_date: "",
      leave_group_name: "",
      reporting_manager: "",
      approved_datetime: "",
    },
    leaveTypes: [],
    formTitle: "New Leave Application",
    dialogEmployees: false,
    idsEmployeeList: [],
    //editor
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",
    // starting editor's content
    content: `
        <h1>Yay Headlines!</h1>
        <p>All these <strong>cool tags</strong> are working now.</p>
          `,

    //end editor
    scrollInvoked: 0,
    start_menu: false,
    end_menu: false,
    title: "",
    des: "",
    desDate: "",
    dept: "",
    options: {},
    Model: "leaves",
    endpoint: "employee_leaves",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],
    loading: false,
    total: 0,
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
    headers: [
      {
        text: "Employee Name",
        align: "left",
        sortable: true,
        filterable: true,
        key: "name",
        value: "name",
      },
      {
        text: "Group Type",
        align: "left",
        filterable: true,
        sortable: true,
        value: "group_name",
      },
      {
        text: "Leave Type",
        align: "left",
        filterable: true,
        sortable: true,
        value: "leave_type_name",
      },
      {
        text: "Star Date",
        align: "left",
        filterable: true,
        sortable: true,
        value: "start_date",
      },
      {
        text: "End Date",
        align: "left",
        filterable: true,
        sortable: true,
        value: "end_date",
      },
      {
        text: "Leave Note",
        align: "left",
        filterable: true,
        sortable: true,
        value: "reason",
      },
      {
        text: "Reporting  ",
        align: "left",
        sortable: true,
        filterable: true,
        value: "reporting",
      },
      {
        text: "Applied On ",
        align: "left",
        sortable: true,
        filterable: true,
        value: "created_at",
      },
      {
        text: "Status",
        align: "left",
        filterable: true,
        sortable: true,
        value: "status",
      },

      { text: "Actions", align: "center", value: "action", sortable: false },
    ],
    editedIndex: -1,
    editedItem: {
      leave_type_id: "",
      reason: "",
      start_date: null,
      end_date: null,
    },
    defaultItem: {
      leave_type_id: "",
      reason: "",

      start_date: null,
      end_date: null,
    },
    response: "",
    data: [],
    //errors: [],
    options_dialog: {},
    employees_dialog: [],
    selectAllDepartment: false,
    selectAllEmployee: false,
    DialogEmployeesData: {},
    todayDate: "",
    login_user_employee_id: "",
  }),

  computed: {},

  watch: {},

  created() {
    this.loading = true;
    this.errors = {
      status: false,

      value: [""],
    };
    this.errorsFileUpload = [];

    this.getDataFromApi();
    if (this.$auth.user.employee.leave_group_id)
      this.getLeaveTypesByGroupId(this.$auth.user.employee.leave_group_id);

    let now = new Date();

    let year = now.getFullYear();
    let day = ("0" + now.getDate()).slice(-2);
    let month = ("0" + (now.getMonth() + 1)).slice(-2);

    let formattedDateTime = year + "-" + month + "-" + day;

    this.todayDate = formattedDateTime;
    // setInterval(() => {
    //   this.getDataFromApi();
    // }, 1000 * 60 * 60);

    this.gotoGroupDetails();
  },

  methods: {
    openDialogUploadDocuments() {
      // this.Document = {
      //   items: [],
      // };
      // this.Document.items.push({
      //   title: "",
      //   file: "",
      // });
      this.dialogUploadDocuments = true;
    },
    closeDialogUploadDocuments() {
      this.Document = {
        items: [],
      };
      this.Document.items.push({
        title: "",
        file: "",
      });
      this.errors = [];
      this.errorsFileUpload = [];
      this.dialogUploadDocuments = false;
    },
    SaveDocumentsDialog() {
      this.dialogUploadDocuments = false;
    },
    verifyAvailableCount(leaveTypeId) {
      let filterObject = this.DialogLeavesList.find(
        (item) => item.leave_type_id === leaveTypeId
      );

      if (filterObject.leave_type_count - filterObject.employee_used >= 0) {
        this.newLeaveApplication = true;
      } else {
        this.newLeaveApplication = false;
      }
      this.leave_available_count =
        filterObject.employee_used + "/" + filterObject.leave_type_count;
    },
    applyFilters(filter_column = "", filter_value = "") {
      this.getDataFromApi("", filter_column, filter_value);
    },
    toggleFilter() {
      this.isFilter = !this.isFilter;
    },
    view(item) {
      this.dialogViewObject.id = item.id;
      this.dialogViewObject.employee_name =
        item.employee.first_name + " " + item.employee.last_name;
      this.dialogViewObject.leave_type = item.leave_type.name;
      this.dialogViewObject.from_date = item.start_date;
      this.dialogViewObject.to_date = item.end_date;
      this.dialogViewObject.approved_manager =
        item.reporting.first_name + " " + item.reporting.last_name;
      this.dialogViewObject.status = item.status;
      this.dialogViewObject.reason = item.reason;
      this.dialogViewObject.applied_date = this.getCurrentDateTime(
        item.created_at
      );
      this.dialogViewObject.leave_group_name = item.employee.leave_group
        ? item.employee.leave_group.group_name
        : "--";
      this.dialogViewObject.approved_datetime = item.updated_at
        ? this.getCurrentDateTime(item.updated_at)
        : "--";
      this.dialogViewObject.reporting_manager = item.reporting
        ? item.reporting.first_name + " " + item.reporting.last_name
        : "--";
      this.dialogViewObject.approve_reject_notes = item.approve_reject_notes;

      this.document_list = [];
      this.dialogView = true;
      this.getInfo(item.id);
    },
    getCurrentDateTime(date) {
      let now = new Date(date);

      let year = now.getFullYear();
      let day = ("0" + now.getDate()).slice(-2);
      let month = ("0" + (now.getMonth() + 1)).slice(-2);
      let hours = ("0" + now.getHours()).slice(-2);
      let minutes = ("0" + now.getMinutes()).slice(-2);
      let seconds = ("0" + now.getSeconds()).slice(-2);

      let formattedDateTime = year + "-" + month + "-" + day; // + " " + hours + ":" + minutes;

      return formattedDateTime;
    },
    update_EdititemStart() {
      this.$refs.from_menu.save(this.editedItem.start_date);
      this.from_menu = false;
    },
    update_EdititemEnd() {
      this.$refs.end_menu.save(this.editedItem.end_date);
      this.end_menu = false;
    },
    gotoGroupDetails(leaveGroupId = "") {
      if (leaveGroupId == "") {
        leaveGroupId = this.$auth.user.employee.leave_group_id;
      }

      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          employee_id: this.$auth.user.employee.id,
        },
      };
      this.$axios
        .get("leave_groups/" + leaveGroupId, options)
        .then(({ data }) => {
          //  this.dialogLeaveGroup = true;
          this.DialogLeaveGroupInfo = data;
          this.DialogLeavesList = data[0].leave_count;
        });
    },
    gotoDialogPage(item) {
      this.DialogEmployeesData = item.employees;
      this.dialogEmployees = true;
    },
    datatable_save() {},
    datatable_cancel() {
      this.datatable_search_textbox = "";
    },
    datatable_open() {
      this.datatable_search_textbox = "";
    },
    datatable_close() {
      this.loading = false;
    },
    toggleDepartmentSelection() {
      this.selectAllDepartment = !this.selectAllDepartment;
    },
    toggleEmployeeSelection() {
      this.selectAllEmployee = !this.selectAllEmployee;
    },
    can(per) {
      return this.$pagePermission.can(per, this);
    },

    onScroll() {
      this.scrollInvoked++;
    },
    openNewDialog() {
      this.editedItem = {
        leave_type_id: "",
        reason: "",
        start_date: null,
        end_date: null,
      };
      this.errors = [];
      this.errorsFileUpload = [];

      this.dialog = true;
      this.document_list = [];
      this.Document = {
        items: [],
      };
      this.Document.items.push({
        title: "",
        file: "",
      });
    },
    getLeaveTypesByGroupId(leaveGroupId) {
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios
        .get("leave_groups/" + leaveGroupId, options)
        .then(({ data }) => {
          this.leaveTypes = data[0].leave_count;
        });
    },

    clearFilters() {
      this.filters = {};
      this.isFilter = false;
      this.getDataFromApi();
    },

    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      let endDate = new Date();

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          year: endDate.getFullYear(),
          employee_id: this.$auth.user.employee.id,
        },
      };
      if (filter_column != "") {
        options.params[filter_column] = filter_value;
      }

      this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
        if (filter_column != "" && data.data.length == 0) {
          this.snack = true;
          this.snackColor = "error";
          this.snackText = "No Results Found";
          this.loading = false;
          //return false;
        }
        this.data = data.data;

        this.total = data.total;
        this.loading = false;
        this.gotoGroupDetails("");

        if (this.$auth)
          if (this.$auth.user)
            this.login_user_employee_id = this.$auth.user.employee.id;
      });
    },

    editItem(item) {
      this.formTitle = "Edit leaves Information";
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.document_list = [];
      this.Document = {
        items: [],
      };
      this.Document.items.push({
        title: "",
        file: "",
      });

      this.errorsFileUpload = [];
      this.getInfo(this.editedItem.id);
      this.verifyAvailableCount(this.editedItem.leave_type_id);
    },

    delteteSelectedRecords() {
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint} / delete /selected`, {
            ids: this.ids.map((e) => e.id),
          })
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.snackbar = data.status;
              this.ids = [];
              this.response = "Selected records has been deleted";
            }
            this.getDataFromApi();
          })
          .catch((err) => console.log(err));
    },

    deleteItem(item) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(this.endpoint + "/" + item.id)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
            }
          })
          .catch((err) => console.log(err));
    },

    close() {
      this.dialog = false;
      this.dialogView = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },

    // getEmployees(url = "employee") {
    //   this.loading = true;

    //   const { page, itemsPerPage } = this.options;

    //   let options = {
    //     params: {
    //       per_page: itemsPerPage,
    //       company_id: this.$auth.user.company_id,
    //     },
    //   };

    //   this.$axios.get(`${url}?page=${page}`, options).then(({ data }) => {
    //     this.employees_dialog = data.data;
    //   });
    // },
    rejectLeave(leaveid) {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios
        .get(this.endpoint + "/reject/" + leaveid, options)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = data.status;
            this.response = data.message;
            this.getDataFromApi();
            this.dialogView = false;
          }
        });
    },
    approveLeave(leaveid) {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };
      this.$axios
        .get(this.endpoint + "/approve/" + leaveid, options)
        .then(({ data }) => {
          if (!data.status) {
            this.errors = data.errors;
          } else {
            this.snackbar = data.status;
            this.response = data.message;
            this.getDataFromApi();
            this.dialogView = false;
          }
        });
    },
    async save() {
      let options = {
        params: {
          company_id: this.$auth.user.company_id,
          employee_id: this.login_user_employee_id,
          reporting_manager_id: this.$auth.user.employee.reporting_manager_id,
          leave_type_id: this.editedItem.leave_type_id,
          start_date: this.editedItem.start_date,
          end_date: this.editedItem.end_date,
          reason: this.editedItem.reason,
        },
      };

      if (this.editedIndex > -1) {
        await this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.save_document_info(this.editedItem.id);

              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, options.params)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.save_document_info(data.record.id);

              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
              this.errors = [];
              this.search = "";
            }
          })
          .catch((res) => console.log(res));
      }
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
    getInfo(leave_id) {
      this.$axios
        .get(`employee_document`, {
          params: {
            company_id: this.$auth?.user?.company?.id,
            employee_id: this.login_user_employee_id,
            leave_id: leave_id,
          },
        })
        .then(({ data }) => {
          this.document_list = data;
          this.loading = false;
        });
    },

    // handleFileChange(file, index) {

    //   if (file) {
    //     if (file && file.size > 1024 * 100) {

    //       this.errorsFileUpload[index] = {
    //         "status": false,
    //         // "approve_reject_notes": [
    //         //   "Notes is required"
    //         // ],
    //         "value": [
    //           "File too big (> 100kb). Upload less than 100Kb"
    //         ]
    //       };
    //       this.snackbar = true;
    //       this.response = file.name + " File too big (> 100kb). Upload less than 100Kb";
    //       return false;

    //     }
    //   }
    // },
    uploadFilesizeValidation(file, index) {
      if (file) {
        if (file && file.size > 1024 * 100) {
          this.errorsFileUpload[index] = {
            status: false,

            value: ["<100Kb.   "],
          };
          return false;
        } else {
          this.errorsFileUpload[index] = {};
          return true;
        }
      }
    },
    save_document_info(leave_id) {
      let options = {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      };
      let payload = new FormData();
      let totalFiles = 0;
      this.Document.items.forEach((e) => {
        if (e.file != "" && this.uploadFilesizeValidation(e.file, totalFiles)) {
          totalFiles++;
          payload.append(`items[][key]`, e.title);
          payload.append(`items[][value]`, e.file || {});
        }
      });
      if (totalFiles) {
        payload.append(`company_id`, this.$auth?.user?.company?.id);
        payload.append(`leave_id`, leave_id);
        payload.append(`employee_id`, this.login_user_employee_id);

        this.$axios
          .post(`employee_document`, payload, options)
          .then(({ data }) => {
            this.loading = false;

            if (!data.status) {
              //this.errorsFileUpload = data.errors;
            } else {
              // this.errorsFileUpload = [];
              this.snackbar = true;
              this.response = data.message;
              this.getInfo(leave_id);
              //this.Document.items = [{ title: "", file: "" }];
              //this.close_document_info();
            }
          })
          .catch((e) => console.log(e));
      }
    },

    close_document_info() {
      this.documents = false;
      this.errors = [];
    },

    removeItem(index) {
      this.errorsFileUpload[index] = {
        status: true,
        value: [],
      };
      this.Document.items.splice(index, 1);
    },

    delete_document(id, leave_id) {
      confirm(
        "Are you sure you wish to delete , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .delete(`employee_document/${id}`)
          .then(({ data }) => {
            this.loading = false;

            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.errors = [];
              this.snackbar = true;
              this.response = data.message;
              this.getInfo(leave_id);
              this.close_document_info();
            }
          })
          .catch((e) => console.log(e));
    },
  },
};
</script>
