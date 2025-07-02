import pymysql
import requests
import os
import random
import string
import time

class Client:
    def __init__(self, token, db_id, db_pw, server_addr):
        # 사용자 이름 및 접속 정보
        self.token = token
        self.flag_addr = f"http://{server_addr}/?token={token}"

        # 사용자 DB 정보
        self.db_id = db_id
        self.db_pw = db_pw

    # 서버에서 FLAG 받아오기
    def get_flag(self):
        print(self.flag_addr)
        all_flag = requests.get(self.flag_addr).text
        all_flag_lst = all_flag.split("_")
        self.db_flag = all_flag_lst[0]
        self.server_flag = all_flag_lst[1]

    # 데이터베이스에 FLAG 저장
    def save_flag_db(self):
        char_set = string.ascii_lowercase + string.digits
        new_tbl = ''.join(random.sample(char_set * 6, 6))

        db = pymysql.connect(
            host="kknock6.mysql.database.azure.com",
            user=self.db_id,
            password=self.db_pw,
            db="flag"
        )
        cursor = db.cursor()

        # 기존 테이블 이름 가져오기
        cursor.execute("SHOW TABLES")
        before_tbl = cursor.fetchone()[0]

        # 테이블 구조 확인
        cursor.execute(f"DESC {before_tbl}")
        columns = cursor.fetchall()  # [(Field, Type, ...), ...]

        # 문자열 컬럼 찾기
        str_col = None
        for col in columns:
            field, ctype = col[0], col[1].lower()
            if any(s in ctype for s in ['varchar', 'char', 'text']):
                str_col = field
                break

        if not str_col:
            raise Exception("문자열 타입 컬럼이 없어 FLAG를 저장할 수 없습니다.")

        # 테이블명 변경
        change_tbl = f"RENAME TABLE {before_tbl} TO {new_tbl}"
        cursor.execute(change_tbl)

        # FLAG 저장
        sql = f"UPDATE {new_tbl} SET {str_col} = %s"
        cursor.execute(sql, (self.db_flag,))

        db.commit()
        db.close()

    # 서버에 FLAG 저장
    def save_flag_server(self):
        directory = "/flag"
        if not os.path.exists(directory):
            os.makedirs(directory)

        with open("/flag/flag", "w", encoding="UTF-8") as f:
            f.write(f"{self.server_flag}\n")


if __name__ == "__main__":
    client = Client(
        token="my_server_is_dead_or_hacked_by_who",
        db_id="hackers458",
        db_pw="swjisj123!",
        server_addr="token.kknock.org"
    )
    time.sleep(30)
    client.get_flag()
    client.save_flag_db()
    client.save_flag_server()
