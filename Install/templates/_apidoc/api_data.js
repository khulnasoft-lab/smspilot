define({ "api": [
  {
    "type": "POST",
    "url": "/create/contact?key=API_KEY",
    "title": "Create Contact",
    "description": "<p>Create and save a new contact to your account</p>",
    "name": "Create_Contact",
    "group": "Address_Book",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "phone",
            "description": "<p>Contact mobile number, it must satisfy &quot;E164&quot; format</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "name",
            "description": "<p>Contact name</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "group",
            "description": "<p>ID of contact group where you want to save this contact</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Contact saved sauccessfully!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Address_Book"
  },
  {
    "type": "POST",
    "url": "/create/group?key=API_KEY",
    "title": "Create Group",
    "description": "<p>Create and save a new contact group to your account</p>",
    "name": "Create_Group",
    "group": "Address_Book",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "name",
            "description": "<p>Name of contact group</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Contact group saved successfully!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Address_Book"
  },
  {
    "type": "GET",
    "url": "/delete/contact?key=API_KEY",
    "title": "Delete Contact",
    "description": "<p>Delete saved contact number from your account</p>",
    "name": "Delete_Contact",
    "group": "Address_Book",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "id",
            "description": "<p>ID of contact number</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Contact number deleted successfully!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Address_Book"
  },
  {
    "type": "GET",
    "url": "/delete/group?key=API_KEY",
    "title": "Delete Group",
    "description": "<p>Delete contact group from your account</p>",
    "name": "Delete_Group",
    "group": "Address_Book",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "id",
            "description": "<p>ID of contact group</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Contact group deleted successfully!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Address_Book"
  },
  {
    "type": "GET",
    "url": "/get/contacts?key=API_KEY",
    "title": "Get Contacts",
    "description": "<p>Get the list of your saved contacts</p>",
    "name": "Get_Contacts",
    "group": "Address_Book",
    "version": "1.0.0",
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"List of saved contacts\",\n \"data\": [\n            {\n               \"gid\": 1, // group id\n               \"group\": \"Friends\", \n               \"phone\":\"+639123456789\",\n               \"name\":\"Martino Salesi\"\n            },\n            {\n               \"gid\": 5, // group id\n               \"group\": \"Default\", \n               \"phone\":\"+639123455678\",\n               \"name\":\"Danny Flask\"\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Address_Book"
  },
  {
    "type": "GET",
    "url": "/get/groups?key=API_KEY",
    "title": "Get Groups",
    "description": "<p>Get the list of your cantact groups</p>",
    "name": "Get_Groups",
    "group": "Address_Book",
    "version": "1.0.0",
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"List of contact groups\",\n \"data\": [\n            {\n               \"id\": 1,\n               \"name\":\"Friends\"\n            },\n            {\n               \"id\": 5,\n               \"name\":\"Default\"\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Address_Book"
  },
  {
    "type": "GET",
    "url": "/get/device?key=API_KEY",
    "title": "Get Device",
    "description": "<p>Get details about a registered device on your account</p>",
    "name": "Get_Device",
    "group": "Devices",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "id",
            "description": "<p>ID of the device</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Device information for SMFH-07\",\n \"data\": {\n           \"name\": \"OPPO-F11\",\n           \"version\": 9,\n           \"version_name\": \"Android Pie\",\n           \"manufacturer\": \"Oppo\",\n           \"messages\": {\n                \"sent\": 88, // total sent messages\n                \"received\": 62 // total received messages\n            },\n           \"timestamp\": 1234567891234 // registration timestamp\n        }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Device doesn't exist!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Devices"
  },
  {
    "type": "GET",
    "url": "/get/devices?key=API_KEY",
    "title": "Get Devices",
    "description": "<p>Get the list of registered devices on your account</p>",
    "name": "Get_Devices",
    "group": "Devices",
    "version": "1.0.0",
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"List of registered devices\",\n \"data\": [\n            {\n               \"id\": 1,\n               \"name\": \"OPPO-F11\",\n               \"version\": 9,\n               \"version_name\": \"Android Pie\",\n               \"manufacturer\": \"Oppo\",\n               \"timestamp\": 1234567891234 // registration timestamp\n            },\n            {\n               \"id\": 24,\n               \"name\": \"SMJF1-SH\",\n               \"version\": 10,\n               \"version_name\": \"Android 10\",\n               \"manufacturer\": \"Samsung\",\n               \"timestamp\": 1234567899999 // registration timestamp\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Devices"
  },
  {
    "type": "GET",
    "url": "/get/pending?key=API_KEY",
    "title": "Get Pending",
    "description": "<p>Get the list of pending messages for sending</p>",
    "name": "Get_Pending",
    "group": "Messages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "device",
            "description": "<p>ID of the specific device you want to get pending messages (Optional)</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Messages waiting to be sent\",\n \"data\": [\n            {\n               \"api\": true,\n               \"sim\": 0,\n               \"phone\": \"+639123456789\",\n               \"device\": 1, // id of device used for sending\n               \"message\": \"This is the message\",\n               \"priority\": false,\n               \"timestamp\": \"1234567899999\" // timestamp of creation\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 404,\n \"message\": \"Device doesn't exist!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Messages"
  },
  {
    "type": "GET",
    "url": "/get/received?key=API_KEY",
    "title": "Get Received",
    "description": "<p>Get the list of received messages on your account</p>",
    "name": "Get_Received",
    "group": "Messages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "limit",
            "description": "<p>Number of results to return, default is 10 (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page",
            "description": "<p>Pagination number to navigate result sets (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "device",
            "description": "<p>Get received messages from specific device (Optional)</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"List of sent messages\",\n \"data\": [\n            {\n               \"device\": 1, // id of device origin, 0 if device was deleted\n               \"phone\": \"+639123456789\",\n               \"message\": \"This is the message\",\n               \"timestamp\": 1234567891234 // timestamp of receive\n            },\n            {\n               \"device\": 1, // id of device origin, 0 if device was deleted\n               \"phone\": \"+639123456789\",\n               \"message\": \"This is another message\",\n               \"timestamp\": 1234567899999 // timestamp of receive\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 404,\n \"message\": \"Device doesn't exist!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Messages"
  },
  {
    "type": "GET",
    "url": "/get/sent?key=API_KEY",
    "title": "Get Sent",
    "description": "<p>Get the list of sent messages on your account</p>",
    "name": "Get_Sent",
    "group": "Messages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "limit",
            "description": "<p>Number of results to return, default is 10 (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "page",
            "description": "<p>Pagination number to navigate result sets (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "device",
            "description": "<p>Get messages only from specific device (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "api",
            "description": "<p>Only get sent messages by API (Optional) <br> 1 = Yes <br> 0 = No (Default)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "priority",
            "description": "<p>Only get prioritized sent messages (Optional) <br> 1 = Yes <br> 0 = No (Default)</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"List of sent messages\",\n \"data\": [\n            {\n               \"sim\": 1, // sim slot\n               \"api\": true,\n               \"device\": 1, // id of device used for sending, 0 if device was deleted\n               \"phone\": \"+639123456789\",\n               \"message\": \"This is the message\",\n               \"priority\": false,\n               \"timestamp\": 1234567891234 // timestamp of creation\n            },\n            {\n               \"sim\": 2, // sim slot\n               \"api\": false,\n               \"device\": 34, // id of device used for sending, 0 if device was deleted\n               \"phone\": \"+639123456789\",\n               \"message\": \"This is another message\",\n               \"priority\": true,\n               \"timestamp\": 1234567899999 // timestamp of creation\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 404,\n \"message\": \"Device doesn't exist!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Messages"
  },
  {
    "type": "POST",
    "url": "/send?key=API_KEY",
    "title": "Send Message",
    "description": "<p>Send an sms to defined phone recipient</p>",
    "name": "Send",
    "group": "Messages",
    "version": "1.0.0",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "phone",
            "description": "<p>Recipient mobile number, must satisfy E164 format</p>"
          },
          {
            "group": "Parameter",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Message to be sent to recipient</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "device",
            "description": "<p>ID of device where you want to send the message, default is automatic (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "sim",
            "description": "<p>Sim slot number for sending message, if the slot is not available, default slot will be used. Default is &quot;1&quot; (Optional)</p>"
          },
          {
            "group": "Parameter",
            "type": "int",
            "optional": false,
            "field": "priority",
            "description": "<p>Send the message as priority (Optional)</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Response Format": [
          {
            "group": "Response Format",
            "type": "int",
            "optional": false,
            "field": "status",
            "description": "<p>Status code handler <br/> 200 = Success <br/> 500 = Fail</p>"
          },
          {
            "group": "Response Format",
            "type": "string",
            "optional": false,
            "field": "message",
            "description": "<p>Status response message</p>"
          },
          {
            "group": "Response Format",
            "type": "array",
            "optional": false,
            "field": "data",
            "description": "<p>Additional array of data</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success Example",
          "content": "{\n \"status\": 200,\n \"message\": \"Message has been queued for sending on JFH4-CF\",\n \"data\": [\n            {\n               \"name\": \"Johnny Sins\", // recipient name\n               \"phone\": \"+6391234567890\" // recipient mobile number, E164 formatted\n               \"slot\": 1, // sim slot number\n               \"device\": 2, // id of the device used for sending\n               \"timestamp\": 1234567890123 // creation timestamp\n            }\n        ]\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Failed Example",
          "content": "{\n \"status\": 400,\n \"message\": \"Something went wrong!\",\n \"data\": false\n}",
          "type": "json"
        }
      ]
    },
    "filename": "system/controllers/api.php",
    "groupTitle": "Messages"
  }
] });
