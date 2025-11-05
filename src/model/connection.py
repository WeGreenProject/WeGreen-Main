import json
import mysql.connector
from mysql.connector import Error

host = "localhost"
user = "root"
password = ""
database_name = "WeGreen"

if __name__ == "__main__":
    try:
        connection = mysql.connector.connect(
            host=host, user=user, password=password, database=database_name
        )
        if connection.is_connected():
            print(json.dumps({'status': 'success', 'msg': 'Connected to the database'}))
    except Error as e:
        print(json.dumps({'status': 'error', 'msg': 'Connection failed: ' + str(e)}))