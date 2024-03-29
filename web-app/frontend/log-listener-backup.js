const WebSocket = require("ws");
const fs = require("fs");
require("dotenv").config();

const options = {
  year: "numeric",
  month: "2-digit",
  day: "2-digit",
  hour: "2-digit",
  minute: "2-digit",
  second: "2-digit",
  hour12: false, // Use 24-hour format
  timeZone: "Asia/Dubai",
};

const verification_methods = {
  "1": "Card",
  "2": "Fing",
  "3": "Face",
  "4": "Fing + Card",
  "5": "Face + Fing",
  "6": "Face + Card",
  "7": "Card + Pin",
  "8": "Face + Pin",
  "9": "Fing + Pin",
  "10": "Manual",
  "11": "Fing + Card + Pin",
  "12": "Face + Card + Pin",
  "13": "Face + Fing + Pin",
  "14": "Face + Fing + Card",
  "15": "Repeated",
};


const reasons = {
  "16": "Date Expire",
  "17": "Timezone Expire",
  "18": "Holiday",
  "19": "Unregistered",
  "20": "Detection lock",
  "23": "Loss Card",
  "24": "Blacklisted",
  "25": "Without Verification",
  "26": "No Card Verification",
  "27": "No Fingerprint",
}

const [newDate, newTime] = new Intl.DateTimeFormat("en-US", options)
  .format(new Date())
  .split(",");
const [m, d, y] = newDate.split("/");
const formattedDate = `${d.padStart(2, 0)}-${m.padStart(2, 0)}-${y}`;
const formattedMonth = `${m.padStart(2, 0)}-${y}`;

const logFilePath = `../backend/storage/app/logs/backup/logs-${formattedMonth}.csv`;
const logFilePathRawData = `../backend/storage/app/logs/backup/logs-data-${formattedMonth}.txt`;
const logFilePathAlarm = `../backend/storage/app/alarm/backup/alarm-logs-${formattedMonth}.csv`;

const { SOCKET_ENDPOINT } = process.env;

// Create a WebSocket connection
const socket = new WebSocket(SOCKET_ENDPOINT);

// Handle WebSocket connection events
socket.onopen = () => {
  console.log(`Connected to ${SOCKET_ENDPOINT}`);
};

socket.onerror = (error) => {
  console.error("WebSocket error:", error.message);
};
// Handle WebSocket close event
socket.onclose = (event) => {
  console.error(
    `WebSocket connection closed with code ${event.code
    } at ${formattedDate} ${newTime.trim()}`
  );
};

socket.onmessage = ({ data }) => {
  try {
    fs.appendFileSync(logFilePathRawData, data + "\n");
  } catch (error) {
    console.error("Error processing message:", error.message);
  }
  try {
    const jsonData = JSON.parse(data).Data;

    const { UserCode, SN, RecordDate, RecordNumber, RecordCode } = jsonData;

    if (UserCode > 0) {
      let status = RecordCode > 15 ? "Access Denied" : "Allowed";

      let mode = verification_methods[RecordCode] ?? "---";

      let reason = reasons[RecordCode] ?? "---";


      const logEntry = `${UserCode},${SN},${RecordDate},${RecordNumber},${status},${mode},${reason}`;
      fs.appendFileSync(logFilePath, logEntry + "\n");
      console.log(logEntry);
    } else {
      // console.log(data);
    }
    //Alarm Code
    if (UserCode == 0 && RecordCode == 19) {
      const alarm_logEntry = `${SN},${RecordDate}`;
      fs.appendFileSync(logFilePathAlarm, alarm_logEntry + "\n");
      console.log("Alarm", alarm_logEntry);
    }
  } catch (error) {
    console.error("Error processing message:", error.message);
  }
};

process.on("SIGTERM", () => {
  console.log(`Prcess killed at ${formattedDate} ${newTime.trim()}`);
  process.exit(0); // Exit the process gracefully
});

process.on("SIGINT", () => {
  console.log(`Prcess killed at ${formattedDate} ${newTime.trim()}`);
  process.exit(0); // Exit the process gracefully
});
