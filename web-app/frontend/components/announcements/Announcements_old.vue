<template>
  <div v-if="can(`announcement_access`)">
    <div class="text-center ma-2">
      <v-snackbar v-model="snackbar" top="top" color="secondary" elevation="24">
        {{ response }}
      </v-snackbar>
    </div>
    <v-dialog persistent v-model="dialog" max-width="60%" :key="editedIndex">
      <v-card>
        <v-card-title dense class="popup_background">
          {{ formTitle }} {{ Model }}
          <v-spacer></v-spacer>
          <v-icon @click="dialog = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="4">
                <label for="">Title</label>
                <v-text-field
                  dense
                  outlined
                  v-model="editedItem.title"
                  placeholder="Title"
                  :error-messages="
                    errors && errors.title ? errors.title[0] : ''
                  "
                ></v-text-field>
              </v-col>
              <!-- {{ employees_dialog }} -->
              <v-col cols="4">
                <label for="">Branch</label>
                <v-select
                  v-model="editedItem.branch_id"
                  :items="branchesList"
                  dense
                  placeholder="Branch"
                  outlined
                  item-value="id"
                  item-text="branch_name"
                  :error="errors.branch_id"
                  :error-messages="
                    errors && errors.branch_id ? errors.branch_id[0] : ''
                  "
                  @change="getDepartments()"
                >
                </v-select>
              </v-col>
              <v-col cols="4">
                <label for="">Department</label>
                <v-autocomplete
                  class="announcement-dropdown1"
                  outlined
                  dense
                  @change="employeesByDepartment"
                  v-model="editedItem.departments"
                  :items="departments"
                  multiple
                  item-text="name"
                  item-value="id"
                  placeholder="Departments"
                  :error-messages="
                    errors && errors.departments ? errors.departments[0] : ''
                  "
                >
                  <template v-if="departments.length" #prepend-item>
                    <v-list-item @click="toggleDepartmentSelection">
                      <v-list-item-action>
                        <v-checkbox
                          @click="toggleDepartmentSelection"
                          v-model="selectAllDepartment"
                          :indeterminate="isIndeterminateDepartment"
                          :true-value="true"
                          :false-value="false"
                        ></v-checkbox>
                      </v-list-item-action>
                      <v-list-item-content>
                        <v-list-item-title>
                          {{
                            selectAllDepartment ? "Unselect All" : "Select All"
                          }}
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </template>
                  <template v-slot:selection="{ item, index }">
                    <span
                      v-if="index === 0 && editedItem.departments.length == 1"
                      >{{ item.name }}</span
                    >
                    <span
                      v-else-if="
                        index === 1 &&
                        editedItem.departments.length == departments.length
                      "
                      class=" "
                    >
                      All Selected
                    </span>
                    <span v-else-if="index === 1" class=" ">
                      Selected {{ editedItem.departments.length }} Department(s)
                    </span>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="4">
                <label for="">Employee</label>
                <v-autocomplete
                  class="announcement-dropdown1"
                  outlined
                  dense
                  v-model="editedItem.employees"
                  :items="employees_dialog"
                  multiple
                  item-text="name_with_user_id"
                  item-value="id"
                  placeholder="Employees"
                  :error-messages="
                    errors && errors.employees ? errors.employees[0] : ''
                  "
                  color="background"
                >
                  <template v-if="employees_dialog.length" #prepend-item>
                    <v-list-item @click="toggleEmployeeSelection">
                      <v-list-item-action>
                        <v-checkbox
                          @click="toggleEmployeeSelection"
                          v-model="selectAllEmployee"
                          :indeterminate="isIndeterminateEmployee"
                          :true-value="true"
                          :false-value="false"
                        ></v-checkbox>
                      </v-list-item-action>
                      <v-list-item-content>
                        <v-list-item-title>
                          {{
                            selectAllEmployee ? "Unselect All" : "Select All"
                          }}
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </template>
                  <template v-slot:selection="{ item, index }">
                    <span v-if="index === 0 && editedItem.employees.length == 1"
                      >{{ item.first_name }} {{ item.last_name }}</span
                    >
                    <span
                      v-else-if="
                        index === 1 &&
                        editedItem.employees.length == employees_dialog.length
                      "
                      class=" "
                    >
                      All Selected
                    </span>
                    <span v-else-if="index === 1" class=" ">
                      Selected {{ editedItem.employees.length }} Employee(s)
                    </span>
                  </template>
                  <!-- <template v-slot:selection="{ item, index }">

                    <span v-if="index === 0">{{ item.first_name }} {{ item.last_name }}</span>

                    <span v-else-if="index === 1" class="grey--text text-caption">
                      (+{{ editedItem.employees.length - 1 }} others)
                    </span>
                  </template> -->
                </v-autocomplete>
              </v-col>

              <v-col cols="4">
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
                    <div class="mb-1">Start Date</div>
                    <v-text-field
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
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="from_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.from_menu.save(editedItem.start_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col cols="4">
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
                    <div class="mb-1">End Date</div>
                    <v-text-field
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
                    :min="editedItem.start_date"
                    v-model="editedItem.end_date"
                    no-title
                    scrollable
                  >
                    <v-spacer></v-spacer>
                    <v-btn text color="primary" @click="end_menu = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.end_menu.save(editedItem.end_date)"
                    >
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-menu>
              </v-col>
              <v-col>
                <label for="">Category</label>
                {{ editedItem.category_id }}
                <v-select
                  dense
                  outlined
                  :items="categories"
                  item-value="id"
                  item-text="name"
                  v-model="editedItem.category_id"
                  :error-messages="
                    errors && errors.category_id ? errors.category_id[0] : ''
                  "
                >
                </v-select>
              </v-col>
              <v-col cols="12">
                <ClientOnly>
                  <tiptap-vuetify
                    class="tiptap-icon"
                    v-model="editedItem.description"
                    :extensions="extensions"
                    v-scroll.self="onScroll"
                    max-height="400"
                    :toolbar-attributes="{
                      color: 'background red--text',
                    }"
                  />
                  <template #placeholder> Loading... </template>
                </ClientOnly>
                <!-- <label for="">Description</label> -->
                <!-- <v-textarea
                  dense
                  outlined
                  v-model="editedItem.description"
                  :error-messages="
                    errors && errors.description ? errors.description[0] : ''
                  "
                >
                </v-textarea> -->
                <!-- <ClientOnly>
                  <tiptap-vuetify
                    v-model="editedItem.description"
                    :extensions="extensions"
                    v-scroll.self="onScroll"
                    max-height="300"
                    :toolbar-attributes="{
                      color: 'primary lighten-2 red--text text--lighten-1',
                    }"
                  />
                  <template #placeholder> Loading... </template>
                </ClientOnly> -->
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <!-- <v-btn class="error" small @click="close"> Cancel </v-btn> -->
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog persistent v-model="dialogEmployees" max-width="60%">
      <v-card>
        <v-card-title dense class="popup_background">
          Employees List
          <v-spacer></v-spacer>
          <v-icon @click="dialogEmployees = false" outlined dark>
            mdi mdi-close-circle
          </v-icon>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-data-table
                v-model="idsEmployeeList"
                item-key="id"
                :headers="headers_Dialog"
                :items="DialogEmployeesData"
                :loading="loading"
                :footer-props="{
                  itemsPerPageOptions: [10, 50, 100, 500, 1000],
                }"
                class="elevation-1"
              >
                <template v-slot:item.first_name="{ item }">
                  {{ item.first_name ? item.first_name : "---" }}
                  {{ item.last_name }}
                </template>
                <template v-slot:item.employeeId="{ item }">
                  {{ item.employee_id }}
                </template>
                <!-- <template v-slot:item.department.name="{ item }">
                  {{ (item.department.name) }}
                </template>
                <template v-slot:item.designation.name="{ item }">
                  {{ (item.designation.name) }}
                </template> -->
              </v-data-table>
            </v-row>
          </v-container>
        </v-card-text>

        <!-- <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="error" small @click="close"> Cancel </v-btn>
          <v-btn class="primary" small @click="save">Save</v-btn>
        </v-card-actions> -->
      </v-card>
    </v-dialog>

    <v-row>
      <v-col md="12">
        <v-card class="mb-5 mt-2 rounded-md" elevation="0">
          <v-toolbar class="rounded-md" dense flat>
            <v-toolbar-title
              ><span> {{ Model }} List </span></v-toolbar-title
            >
            <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Reload"
            >
              <v-icon class="ml-2" @click="getDataFromApi()" dark
                >mdi mdi-reload</v-icon
              >
            </v-btn>
            <!-- </template>
              <span>Reload</span>
            </v-tooltip> -->
            <!-- <v-tooltip top color="primary">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Filter"
            >
              <v-icon @click="toggleFilter" class="mx-1 ml-2"
                >mdi mdi-filter</v-icon
              >
            </v-btn>
            <!-- </template>
              <span>Filter</span>
            </v-tooltip> -->
            <v-spacer></v-spacer>
            <!-- <v-tooltip top color="primary" v-if="can(`announcement_create`)">
              <template v-slot:activator="{ on, attrs }"> -->
            <v-btn
              v-if="can(`announcement_create`)"
              dense
              class="ma-0 px-0"
              x-small
              :ripple="false"
              text
              title="Announcement"
            >
              <v-icon class="ml-2" @click="dialog = true" dark
                >mdi mdi-plus-circle</v-icon
              >
            </v-btn>
            <!-- </template>
              <span>New Announcement</span>
            </v-tooltip> -->
          </v-toolbar>
          <!-- <v-text-field class=" form-control py-0 ma-1 mb-0 w-25 float-start custom-text-box floating
                  shadow-none" placeholder="Search..." solo flat @input="searchIt" v-model="search"
                  :hide-details="true"></v-text-field> -->
          <v-snackbar v-model="snack" :timeout="3000" :color="snackColor">
            {{ snackText }}

            <template v-slot:action="{ attrs }">
              <v-btn v-bind="attrs" text @click="snack = false"> Close </v-btn>
            </template>
          </v-snackbar>

          <v-data-table
            v-if="can(`announcement_view`)"
            v-model="ids"
            item-key="id"
            :headers="headers"
            :items="data"
            :loading="loading"
            :options.sync="options"
            :footer-props="{
              itemsPerPageOptions: [10, 50, 100, 500, 1000],
            }"
            class="elevation-1"
            :server-items-length="totalRowsCount"
          >
            <template v-slot:header="{ props: { headers } }">
              <tr v-if="isFilter">
                <td v-for="(header, index) in headers" :key="index">
                  <v-container>
                    <TextField
                      :header="header"
                      column="title"
                      @entered-value="handleFilter"
                    />
                    <DropDown
                      :disabled="true"
                      :header="header"
                      column="department"
                      @entered-value="handleFilter"
                      :items="departments"
                    />
                    <DropDown
                      :disabled="true"
                      :header="header"
                      column="employees"
                      @entered-value="handleFilter"
                      :items="employees"
                    />
                    <DropDown
                      :disabled="false"
                      :header="header"
                      column="categories"
                      @entered-value="handleFilter"
                      :items="categories"
                    />
                    <TextField
                      :header="header"
                      column="description"
                      @entered-value="handleFilter"
                    />
                    <DateRangePicker
                      :disabled="false"
                      :header="header"
                      column="date_range"
                      @selected-dates="handleDatesFilter"
                    />
                  </v-container>
                </td>

                <!-- fieldType -->
                <!-- <DateRangePicker
                    v-if="
                      header.column == 'date_range' &&
                      header.fieldType == 'date_range_picker'
                    "
                  /> -->
                <!-- <v-menu
                    v-if="header.filterSpecial && header.value == 'start_date'"
                    ref="from_menu_filter"
                    v-model="from_menu_filter"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        clearable
                        @click:clear="
                          filters[header.value] = '';
                          applyFilters();
                        "
                        :hide-details="!from_date_filter"
                        outlined
                        dense
                        v-model="filters[header.value]"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        placeholder="Select Date"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      style="height: 350px"
                      v-model="filters[header.value]"
                      no-title
                      scrollable
                      @input="applyFilters()"
                    >
                      <v-spacer></v-spacer>

                      <v-btn
                        text
                        color="primary"
                        @click="
                          filters[header.value] = '';
                          from_menu_filter = false;
                          applyFilters();
                        "
                      >
                        Clear
                      </v-btn>
                    </v-date-picker>
                  </v-menu>
                  <v-menu
                    v-if="header.filterSpecial && header.value == 'end_date'"
                    ref="to_menu_filter"
                    v-model="to_menu_filter"
                    :close-on-content-click="false"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        clearable
                        @click:clear="
                          filters[header.value] = '';
                          applyFilters();
                        "
                        :hide-details="!to_date_filter"
                        outlined
                        dense
                        v-model="filters[header.value]"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        placeholder="Select Date"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      clearable
                      style="height: 350px"
                      v-model="filters[header.value]"
                      no-title
                      scrollable
                      @input="applyFilters()"
                    >
                      <v-spacer></v-spacer>

                      <v-btn
                        text
                        color="primary"
                        @click="
                          filters[header.value] = '';
                          to_menu_filter = false;
                          applyFilters();
                        "
                      >
                        Clear
                      </v-btn>
                    </v-date-picker>
                  </v-menu> -->
              </tr>
            </template>

            <template v-slot:item.start_date="{ item }">
              {{ item && item.start_date }} - {{ item && item.end_date }}
            </template>
            <template v-slot:item.categories="{ item }">
              {{ item.category && item.category.name }}
            </template>
            <template v-slot:item.priority="{ item }">
              {{ item.category && item.category.priority }}
            </template>
            <template v-slot:item.description="{ item }">
              <div class="breakthewords" v-html="item.description"></div>
            </template>
            <template v-slot:item.user="{ item }">
              {{ getUserDetails(item) }}
            </template>
            <template v-slot:item.action="{ item }">
              <v-menu bottom left>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark-2 icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                  </v-btn>
                </template>
                <v-list width="120" dense>
                  <!-- <v-list-item @click="gotoDialogPage(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon color="primary" small> mdi-view-list </v-icon>
                      View
                    </v-list-item-title>
                  </v-list-item> -->

                  <v-list-item @click="editItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`announcement_edit`)"
                        color="secondary"
                        small
                        @click="editItem(item)"
                      >
                        mdi-pencil
                      </v-icon>
                      Edit
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="deleteItem(item)">
                    <v-list-item-title style="cursor: pointer">
                      <v-icon
                        v-if="can(`announcement_delete`)"
                        color="error"
                        small
                        @click="deleteItem(item)"
                        >mdi-delete
                      </v-icon>
                      Delete
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>

            <template v-slot:item.department.name.id="{ item }">
              <span
                v-for="(dep, index) in item.departments"
                :key="index"
                small
                class="  "
                color="primary"
              >
                {{ dep.name }} <br />
              </span>
            </template>
            <template v-slot:item.employees="{ item }">
              <span
                v-for="(emp, index) in item.employees.slice(0, 4)"
                :key="index"
                small
                class="p-2 ma-1"
                color="primary"
              >
                {{ emp.first_name }} {{ emp.last_name }} -
                {{ emp.employee_id }}

                <br />
              </span>
              <v-chip
                small
                class="primary ma-1"
                style="color: black; margin-left: 10px !important"
                @click="gotoDialogPage(item)"
                v-if="item.employees.length > 4"
              >
                More...
              </v-chip>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
  </div>

  <NoAccess v-else />
</template>
<script>
import DateRangePicker from "../../components/Snippets/Filters/DateRangePicker.vue";
import TextField from "../../components/Snippets/Filters/TextField.vue";
import DropDown from "../../components/Snippets/Filters/DropDown.vue";
import {
  TiptapVuetify,
  Heading,
  Bold,
  Italic,
  Strike,
  Underline,
  Paragraph,
  BulletList,
  OrderedList,
  ListItem,
  Blockquote,
  History,
} from "tiptap-vuetify";

export default {
  components: {
    DateRangePicker,
    TextField,
    DropDown,
    TiptapVuetify,
  },
  data: () => ({
    extensions: [
      History,
      Blockquote,
      Underline,
      Strike,
      Italic,
      ListItem,
      BulletList,
      OrderedList,
      [
        Heading,
        {
          options: {
            levels: [1, 2, 3],
          },
        },
      ],
      Bold,
      Paragraph,
    ],
    branchesList: [],
    categories: [],
    totalRowsCount: 0,
    from_menu_filter: "",
    from_date_filter: "",
    to_date_filter: "",
    to_menu_filter: "",
    showFilters: false,
    filters: {},
    isFilter: false,
    options: {},
    dialogEmployees: false,
    idsEmployeeList: [],
    //editor
    datatable_search_textbox: "",
    filter_employeeid: "",
    snack: false,
    snackColor: "",
    snackText: "",

    //end editor
    scrollInvoked: 0,
    start_menu: false,
    end_menu: false,
    title: "",
    des: "",
    desDate: "",
    dept: "",
    options: {},
    Model: "Announcement",
    endpoint: "announcement",
    search: "",
    snackbar: false,
    dialog: false,
    ids: [],
    departments: [],
    loading: false,
    total: 0,
    headers_Dialog: [
      {
        text: "Name",
        align: "left",
        sortable: true,
        value: "first_name",
      },
      {
        text: "Employee Id",
        align: "left",
        sortable: false,
        value: "employee_id",
      },
    ],
    headers: [
      {
        text: "Title",
        align: "left",
        sortable: true,
        key: "title",
        value: "title",
        fieldType: "text",
      },
      {
        text: "Branch",
        align: "left",
        sortable: true,
        key: "department",
        value: "branch.branch_name",
        fieldType: "dropdown",
      },
      {
        text: "Departments",
        align: "left",
        sortable: true,
        key: "department",
        value: "department.name.id",
        fieldType: "dropdown",
      },
      {
        text: "Employees",
        align: "left",
        sortable: true,
        key: "employees",
        value: "employees",
        fieldType: "dropdown",
      },
      {
        text: "Description",
        align: "left",
        sortable: true,
        key: "description",
        value: "description",
        fieldType: "text",
        class: "breakthewords",
      },
      {
        text: "Date Range",
        align: "left",
        sortable: false,
        key: "date_range",
        value: "start_date",
        fieldType: "date_range_picker",
      },
      {
        text: "Category",
        align: "left",
        sortable: false,
        key: "categories",
        value: "categories",
        fieldType: "dropdown",
      },
      {
        text: "User",
        align: "left",
        sortable: false,
        key: "user",
        value: "user",
        fieldType: "dropdown",
      },

      {
        width: "50px",
        text: "Actions",
        align: "center",
        value: "action",
        sortable: false,
      },
    ],
    editedIndex: -1,
    editedItem: {
      title: "",
      description: "",
      departments: [],
      employees: [],
      start_date: null,
      end_date: null,
      category_id: null,
      branch_id: "",
    },
    defaultItem: {
      title: "",
      description: "",
      departments: [],
      employees: [],
      start_date: null,
      end_date: null,
      category_id: null,
      branch_id: "",
    },
    response: "",
    headerText: "",
    data: [],
    errors: [],
    options_dialog: {},
    employees_dialog: [],
    selectAllDepartment: false,
    selectAllEmployee: false,
    DialogEmployeesData: {},

    employees: [],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New" : "Edit";
    },
    getCurrentDate() {
      // Get the current date
      const date = new Date();

      // Get the year, month, and day from the date object
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },
    isIndeterminateDepartment() {
      return (
        this.editedItem.departments.length > 0 &&
        this.editedItem.departments.length < this.departments.length
      );
    },
    isIndeterminateEmployee() {
      return (
        this.editedItem.employees.length > 0 &&
        this.editedItem.employees.length < this.employees_dialog.length
      );
    },
  },

  watch: {
    selectAllDepartment(value) {
      if (value) {
        this.editedItem.departments = this.departments.map((e) => e.id);
        this.employeesByDepartment();
      } else {
        this.editedItem.departments = [];

        this.getEmployees();
      }
    },

    selectAllEmployee(value) {
      if (value) {
        this.editedItem.employees = this.employees_dialog.map((e) => e.id);
      } else {
        this.editedItem.employees = [];
      }
    },

    dialog(val) {
      val || this.close();
      this.errors = [];
      this.search = "";
    },
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  created() {
    this.loading = true;

    this.getDataFromApi();

    this.getbranchesList();
  },

  methods: {
    getbranchesList() {
      this.payloadOptions = {
        params: {
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(`branches_list`, this.payloadOptions).then(({ data }) => {
        this.branchesList = data;

        if (this.$auth.user.branch_id) {
          this.branch_id = this.$auth.user.branch_id;
        } else {
          // this.branchesList = [
          //   { branch_name: `All Branches`, id: `` },
          //   ,
          //   ...this.branchesList,
          // ];
          this.branch_id = "";
        }
      });
    },

    handleFilter({ key, search_value }) {
      this.getDataFromApi(this.endpoint, key, search_value);
    },
    handleDatesFilter(dates) {
      if (dates.length > 1) {
        this.getDataFromApi(this.endpoint, "dates", dates);
      }
    },
    gotoDialogPage(item) {
      // console.log('item', item);
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

    getDepartments() {
      this.getCategories();

      console.log(this.editedItem.branch_id);
      let options = {
        params: {
          per_page: 1000,
          company_id: this.$auth.user.company_id,
          branch_id: this.editedItem.branch_id,
          //department_ids: this.$auth.user.assignedDepartments,
        },
      };
      this.$axios.get(`departments`, options).then(({ data }) => {
        this.departments = data.data;

        if (this.editItemId > -1) {
        } else {
          this.toggleDepartmentSelection();
        }
      });
    },

    employeesByDepartment() {
      //this.getEmployees();
      this.loading_dialog = true;
      const { page, itemsPerPage } = this.options_dialog;

      let options = {
        params: {
          department_ids: this.editedItem.departments,
          per_page: itemsPerPage,
          page: page,
          company_id: this.$auth.user.company_id,
        },
      };
      this.employees_dialog = [];
      if (!this.editedItem.departments.length) {
        this.getEmployees();
        return;
      }

      this.$axios
        .get("employeesByDepartmentForAnnoucements", options)
        .then(({ data }) => {
          this.employees_dialog = data.data;
          this.loading_dialog = false;

          this.toggleEmployeeSelection();
        });
    },

    toggleFilter() {
      // this.filters = {};
      this.isFilter = !this.isFilter;
    },
    clearFilters() {
      this.filters = {};

      this.isFilter = false;
      this.getDataFromApi();
    },
    getDataFromApi(url = this.endpoint, filter_column = "", filter_value = "") {
      if (url == "") url = this.endpoint;
      this.loading = true;

      let { sortBy, sortDesc, page, itemsPerPage } = this.options;

      let sortedBy = sortBy ? sortBy[0] : "";
      let sortedDesc = sortDesc ? sortDesc[0] : "";
      let options = {
        params: {
          page: page,
          sortBy: sortedBy,
          sortDesc: sortedDesc,
          per_page: itemsPerPage,
          //department_ids: this.$auth.user.assignedDepartments,
          company_id: this.$auth.user.company_id,
          ...this.filters,
        },
      };
      if (filter_column != "") {
        options.params[filter_column] = filter_value;
      }

      this.$axios.get(url, options).then(({ data }) => {
        // if (filter_column != "" && data.data.length == 0) {
        //   this.snack = true;
        //   this.snackColor = "error";
        //   this.snackText = "No Results Found";
        //   this.loading = false;
        //   return false;
        // }
        this.totalRowsCount = this.total = data.total;

        this.data = data.data;
        this.loading = false;
      });
    },
    searchIt(e) {
      if (e.length == 0) {
        this.getDataFromApi();
      } else if (e.length > 2) {
        this.getDataFromApi(`${this.endpoint}/search/${e}`);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.indexOf(item);
      this.editedItem = Object.assign({}, item);

      this.dialog = true;
      this.editedItem.departments = item.departments.map((e) => e.id);
      this.editedItem.employees = item.employees.map((e) => e.id);
      this.getbranchesList();
    },

    delteteSelectedRecords() {
      confirm(
        "Are you sure you wish to delete selected records , to mitigate any inconvenience in future."
      ) &&
        this.$axios
          .post(`${this.endpoint}/delete/selected`, {
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
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      }, 300);
    },
    getCategories(url = "announcements_category") {
      this.categories = [];
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          page,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
          branch_id: this.editedItem.branch_id,
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.categories = data.data;
        this.loading = false;
      });
    },
    getUserDetails(item) {
      if (item != null) {
        if (item.user && item.user.user_type == "company") {
          return item.user.name;
        } else if (item.user && item.user.user_type == "employee") {
          return (
            item.user.employee.first_name + " " + item.user.employee.last_name
          );
        }
      } else {
        return "---";
      }
    },
    getEmployees(url = "employee") {
      this.loading = true;

      const { page, itemsPerPage } = this.options;

      let options = {
        params: {
          page,
          per_page: itemsPerPage,
          company_id: this.$auth.user.company_id,
        },
      };

      this.$axios.get(url, options).then(({ data }) => {
        this.employees_dialog = data.data;
        this.loading = false;
      });
    },

    save() {
      this.editedItem.company_id = this.$auth.user.company_id;

      this.editedItem.user_id = this.$auth.user.id;

      if (this.editedIndex > -1) {
        this.$axios
          .put(this.endpoint + "/" + this.editedItem.id, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
              this.getDataFromApi();
              this.snackbar = data.status;
              this.response = data.message;
              this.close();
            }
          })
          .catch((err) => console.log(err));
      } else {
        this.$axios
          .post(this.endpoint, this.editedItem)
          .then(({ data }) => {
            if (!data.status) {
              this.errors = data.errors;
            } else {
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
  },
};
</script>
<style>
.tiptap-vuetify-editor__content {
  min-height: 400px !important;
}

.ProseMirror .ProseMirror-focused {
  height: 400px !important;
}

.tiptap-icon .v-icon {
  color: white !important;
}

.tiptap-icon .v-btn--icon {
  color: white !important;
}
</style>
