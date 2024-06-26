export default ({ app }, inject) => {
  inject("dateFormat", {
    format1: (inputdate) => {
      // Create a Date object with the date "2023-09-13"  Output Sun, Jan 01, 2023
      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);
      return formattedDate;
    },
    format2: (inputdate) => {
      // Create a Date object with the date "2023-09-13"  Output: "23-09-13"
      const date = new Date(inputdate);

      const year = date.getFullYear().toString().slice(-2);
      const month = (date.getMonth() + 1).toString().padStart(2, "0"); // Note: Month is zero-indexed
      const day = date.getDate().toString().padStart(2, "0");

      return `${year}-${month}-${day}`;
    },
    format3: (inputdate) => {
      const currentDate = new Date(inputdate);

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      return `${year}-${month}-${day} ${hours}:${minutes} `;
    },
    format4: (inputdate) => {
      const currentDate = new Date(inputdate);

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);

      return `${formattedDate}  ${hours}:${minutes} `;
    },
    format5: (inputdate) => {
      const currentDate = new Date(inputdate);

      const year = currentDate.getFullYear();
      const month = (currentDate.getMonth() + 1).toString().padStart(2, "0"); // Adding 1 to month because it's zero-based.
      const day = currentDate.getDate().toString().padStart(2, "0");
      const hours = currentDate.getHours().toString().padStart(2, "0");
      const minutes = currentDate.getMinutes().toString().padStart(2, "0");
      const seconds = currentDate.getSeconds().toString().padStart(2, "0");

      const inputDate = new Date(inputdate);
      const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        weekday: "short",
      };
      const formattedDate = inputDate.toLocaleDateString("en-US", options);

      return `${hours}:${minutes} ${formattedDate}   `;
    },
    monthStartEnd: (inputdate) => {
      // Get the current date
      const currentDate = new Date(inputdate);

      // Get the first day of the current month
      const firstDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth(),
        1
      );

      // Get the last day of the current month
      const lastDayOfMonth = new Date(
        currentDate.getFullYear(),
        currentDate.getMonth() + 1,
        0
      );

      // Format the dates as strings (in 'YYYY-MM-DD' format)
      const formattedFirstDay = `${firstDayOfMonth.getFullYear()}-${(
        firstDayOfMonth.getMonth() + 1
      )
        .toString()
        .padStart(2, "0")}-01`;
      const formattedLastDay = `${lastDayOfMonth.getFullYear()}-${(
        lastDayOfMonth.getMonth() + 1
      )
        .toString()
        .padStart(2, "0")}-${lastDayOfMonth.getDate()}`;

      return { first: formattedFirstDay, last: formattedLastDay };
    },
    minutesToHHMM(minutes) {
      var hours = Math.floor(minutes / 60);
      var remainingMinutes = minutes % 60;
      return (
        (hours < 10 ? "0" : "") +
        hours +
        ":" +
        (remainingMinutes < 10 ? "0" : "") +
        remainingMinutes
      );
    },
    time2Hm: (inputdate) => {
      // Split the time string by ':'
      const timeParts = inputdate.split(":");

      // Extract the hour and minutes
      const hour = timeParts[0];
      const minutes = timeParts[1];

      // console.log(`Hour: ${hour}`);
      // console.log(`Minutes: ${minutes}`);
      return `${hour}:${minutes}`;
    },
    //Output: e.g., 02-04-2024, Monday

    format_date_with_dayname: (inputdate) => {
      // Create a new Date object for today's date
      var today = new Date(inputdate);

      //console.log("today", today);

      // Get the day, month, year, and day of the week
      var day = today.getDate();
      var month = today.getMonth() + 1; // Month is zero-based, so add 1
      var year = today.getFullYear();
      var dayOfWeek = today.getDay(); // 0 for Sunday, 1 for Monday, ..., 6 for Saturday

      //console.log("datodayyOfWeek", dayOfWeek);

      // Ensure day and month are formatted with leading zeros if needed
      day = (day < 10 ? "0" : "") + day;
      month = (month < 10 ? "0" : "") + month;

      // Array to map day of the week number to its name
      var daysOfWeek = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];

      // Get the name of the day of the week
      var dayName = daysOfWeek[dayOfWeek];

      // Construct the desired date format
      var formattedDate = day + "-" + month + "-" + year + ", " + dayName;
      return formattedDate;
      //console.log(formattedDate); // Output: e.g., 02-04-2024, Monday
    },

    format_date66: (inputDate) => {
      ////23/04/2024
      if (!inputDate) return "";
      // Split the date string into year, month, and day
      var parts = inputDate.split("-");

      // Rearrange the parts to form the desired format
      var formattedDate = parts[2] + "/" + parts[1] + "/" + parts[0];

      return formattedDate;
    },
    convertToAMPM(hour) {
      if (hour >= 0 && hour <= 11) {
        return hour + " AM";
      } else if (hour == 12) {
        return hour + " PM";
      } else {
        return hour - 12 + " PM";
      }
    },
    getDayName: (inputDate) => {
      // Create a new Date object
      var today = new Date(inputDate);

      // Get the day of the week (0-6)
      var dayOfWeek = today.getDay();

      // Define an array of day names
      var days = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

      // Retrieve the day name using the day of the week
      return days[dayOfWeek];

      // Output the day of the week
    },
    getDayFullName: (inputDate) => {
      // Create a new Date object
      var today = new Date(inputDate);

      // Get the day of the week (0-6)
      var dayOfWeek = today.getDay();

      // Define an array of day names
      var days = [
        "sunday",
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday",
      ];

      // Retrieve the day name using the day of the week
      return days[dayOfWeek];

      // Output the day of the week
    },
    format_month_name_year: (inputdate) => {
      // Create a Date object with the date "2023-09-13"  Output: "23-09-13"
      const date = new Date(inputdate);

      const months = [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December",
      ];

      const currentDate = date;
      const currentMonth = months[currentDate.getMonth()];
      const currentYear = currentDate.getFullYear();

      return `${currentMonth} ${currentYear}`;
    },

    can(per, thisobj) {
      let u = thisobj.$auth.user;

      return (
        (u && u.permissions.some((e) => e == per || per == "/")) ||
        u.is_master ||
        u.user_type == "branch"
      );
    },
  });

  inject("pagePermission", {
    can(per, thisobj) {
      let u = thisobj.$auth.user;

      // return (
      //   (u && u.permissions.some((e) => e == per || per == "/")) ||
      //   u.is_master ||
      //   u.user_type == "branch"
      // );

      return (
        (u && u.permissions.some((e) => e == per || per == "/")) || u.is_master
      );
    },
  });
};
