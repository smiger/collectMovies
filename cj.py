import sys
from PyQt5.QtWidgets import QApplication
from PyQt5.QtWebEngineWidgets import QWebEngineView
from PyQt5.QtCore import QUrl, QTimer
import datetime

CUR_COUNT = 0
URL_COUNT = 1
RUN_RATE = 24/URL_COUNT
#豆瓣资源网
dbzyz_URL = 'http://127.0.0.1:8000/collect?ac=cj&cjurl=http://www.dbzyz.com/inc/dbm3u8.php&h=24&t=&ids=&wd=&type=1&mid=1&param='

def operate():
    global view
    global CUR_COUNT
    global URL_COUNT
    nowTime=datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    print(nowTime)
    print(CUR_COUNT%URL_COUNT)
    if CUR_COUNT%URL_COUNT == 0:
        view.load(QUrl(dbzyz_URL))

    CUR_COUNT = (CUR_COUNT + 1)%URL_COUNT

if __name__ == '__main__':
    app = QApplication(sys.argv)
    view = QWebEngineView()
    nowTime=datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    print(nowTime)
    print(RUN_RATE)
    timer = QTimer()
    timer.timeout.connect(operate)
    timer.start(RUN_RATE*60*60*1000)
    view.load(QUrl(dbzyz_URL))
    view.show()
    sys.exit(app.exec_())