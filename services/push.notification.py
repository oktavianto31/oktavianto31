#!/usr/bin/env python
# -*- coding: utf-8 -*-
import os,os.path,string,sys, time, psycopg2,logging,logging.handlers,struct,xmlrpc.client,re,http.cookiejar,atexit,json, urllib.request, urllib.error, urllib.parse,urllib.request,urllib.parse,urllib.error
from time import sleep, time, strftime, strptime
from datetime import date,datetime
from daemon import runner
from mechanize import Browser

#!/usr/bin/python

class IonicDaemon():	
	dbname = 'homerun2'
	dbuser = 'homerunadmin'
	dbpwd = 'plokijuh123!!'
	dbhost = '127.0.0.1'
	
#	def __init__(self, pid, log):
#	Daemon.__init__(self, pid, stdout=log, stderr=log)

	def __init__(self):
		self.stdin_path = '/dev/null'
		self.stdout_path = '/tmp/homerun_log/homerun_pushNotification_daemon.log'
		self.stderr_path = '/tmp/homerun_log/homerun_pushNotification_daemon.log'
		self.pidfile_path =  '/tmp/homerun_log/homerun_pushNotification_daemon.pid'
		self.pidfile_timeout = 5
		#self.busy = 0
		

	def shutdown(self):
		print("Stopping Daemon!")
		
	def setDb(self):
		try:
			query = "dbname='%s' user='%s' host='%s' password='%s'" %(self.dbname,self.dbuser,self.dbhost,self.dbpwd)
			self.conn = psycopg2.connect(query)
			self.conn.set_isolation_level(0)
		except psycopg2.OperationalError as e:
			self.writelog(string.strip(e.message))
			return False
		return True
		
	def dbQuery(self,query):
		result = 0
		if query == '':
			return False
		try:
			self.writelog(query)
			c = self.conn.cursor()
			c.execute(query)
			self.conn.commit()
			result = c.fetchall()
			c.close()
		except psycopg2.ProgrammingError as e:
			self.writelog(str(e).lstrip(),3)
		except:
			self.writelog('Error Query')
			return False

		return	result
		
	def dbSelectOne(self,query):
		result = 0
		if query == '':
			return False
		try:
			self.writelog(query)
			c = self.conn.cursor()
			c.execute(query)
			self.conn.commit()
			result = c.fetchone()
			c.close()
		except psycopg2.ProgrammingError as e:
			self.writelog(str(e).lstrip(),3)
		except:
			self.writelog('Error Query')
			return False

		return	result
	
	def dbSelect(self,table,col,whereclause='',limit=''):
		if table == '':
			return []
		select = ','.join(col)
		if select=='':
			select = '*'
		if whereclause != '':
			whereclause = 'where '+whereclause
		if limit != '':
			limit= 'limit %s'%limit
		query = "select %s from %s %s %s" %(select,table,whereclause,limit)
		#self.writelog(query)
		#query = "select modem_id,modem_name,modem_type,modem_port,modem_baud,modem_bitsize,modem_pin,modem_timeout_read,modem_timeout_proc from modem where modem_service = 1"
		try:
			c = self.conn.cursor()
			c.execute(query)
			hasil = c.fetchall()
			c.close()
			return hasil	
		except psycopg2.ProgrammingError as e:
			self.writelog(str(e).lstrip(),3)
		except:
			return []

	
	def dbSelectAll(self,query):
		result = 0
		if query == '':
			return False
		try:
			c = self.conn.cursor()
			result = c.execute(query)
			self.conn.commit()
			c.close()
		except psycopg2.ProgrammingError as e:
			self.writelog(str(e).lstrip(),3)
		except:
			self.writelog('Error Query')
			return False

		return	result
		
	
	def getMember(self,id):
		return self.dbQuery("select customer_id from customer where customer_id = %s"%id)
		
			
	def loggingStart(self):
		LOG_FILENAME = '/tmp/homerun_pushNotification.log'
		self.logger = logging.getLogger('homerun_pushNotification_logger')
		self.logger.setLevel(logging.DEBUG)
		handler = logging.handlers.RotatingFileHandler(LOG_FILENAME, maxBytes=300000, backupCount=200)
		self.logger.addHandler(handler)
	
	def writelog(self,msg,type=1,lvl=1):
		typeStr = 'Info'
		if type==2:
			typeStr = 'Warning'
		elif type==3:
			typeStr = 'Error'	
		time_now = strftime('%d-%m-%Y %H:%M:%S')
		self.logger.debug('[%s]\t%s\t%s' %(time_now,typeStr,msg))
		return True
		
	def stopService(self):
		self.writelog('Service Stopped')
		if self.conn:
			self.conn.close()
		#self.busy = 0
		#self.smtpmail.close()
		sleep(3)
		self.stop()
		sys.exit()

	def restartService(self):
		self.writelog('Service Stopped')
		if self.conn:
			self.conn.close()
		#self.busy = 0
		#self.smtpmail.close()
		sleep(3)
		self.stop()
		sleep(30)
		self.run()
	
	def run(self):
		self.loggingStart()
		self.writelog('Set DB')
		#if not self.setDb():			
			#self.stop()
			#self.stopService()
		#self.writelog('Set Email')
		#if not self.setEmail():
			#self.stop()			
			#self.stopService()
		self.writelog("Start Service...")
		#print "Starting Daemon!"
		#self.busy = 1
		while True:
			#try:
			if not self.setDb():			
				self.restartService()
			push2 = self.dbSelect('push_notification',(
				'push_notification_id',				
				'customer_id',
				'push_notification_text',
				'push_notification_status',
				'push_notification_date',
				'push_notification_title',
				'push_notification_url',
				'push_notification_url_host',
				'push_notification_type',
				'push_notification_token',
				'push_notification_image'
				),'push_notification_schedule_date <= now() and push_notification_status = 0 and push_notification_url_host is not null and push_notification_token is not null and push_notification_date is not null and push_notification_text is not null and push_notification_title is not null order by push_notification_id DESC',1)
				
			if push2 != [] and push2 is not None:	
				for dat in push2:
					sukses = 0
					response = []
					headers = {'Accept':'application/json','Content-Type':'application/json; charset=utf-8','Authorization':'key=AAAAtw10EhI:APA91bEXI6GsrgZOgfpZ3zbFa0Uz8mTMc7RFNEASYyQiwEDGTzkFXeLhRTEFQwLc5f2N5YmqfSY7EFhnH6hWvRR3-JV78bIQzpqI623NWRvyVk9IUeGInOeJqNFX3X785ElSDtPBmc6_'}
					data_search = {
						"to": "%s"%dat[9],
						"priority": "high",
						"restricted_package_name": "com.homerunapp",
						"notification": {
							"title": "%s"%dat[5],
							"body": "%s"%dat[2],
							"image": "%s"%dat[10],
							"color": "#ffffff",
							"sound":"default",
							"click_action":"FLUTTER_NOTIFICATION_CLICK",
						},
						"apns": {
							"payload": {
								"aps": {
									"mutable-content": 1,
								},
							},
							"fcm_options": {
								"image": "%s"%dat[10],
							},
						},
						"data":{
							"click_action":"FLUTTER_NOTIFICATION_CLICK",
							"badge": 1,
							"title": "%s"%dat[5],
							"body": "%s"%dat[2],
							"image": "%s"%dat[10],
							"dl": "id-homerun.com/share/",
							"path": "%s"%dat[6],
							"screen": "%s"%dat[6]
						},
					}
					try:
						req = urllib.request.Request(dat[7])
						req.add_header('Accept', 'application/json')
						req.add_header('Content-Type', 'application/json; charset=utf-8')
						req.add_header('Authorization', 'key=AAAAtw10EhI:APA91bEXI6GsrgZOgfpZ3zbFa0Uz8mTMc7RFNEASYyQiwEDGTzkFXeLhRTEFQwLc5f2N5YmqfSY7EFhnH6hWvRR3-JV78bIQzpqI623NWRvyVk9IUeGInOeJqNFX3X785ElSDtPBmc6_')
						jsondata = json.dumps(data_search)
						jsondataasbytes = jsondata.encode('utf-8')   # needs to be bytes
						req.add_header('Content-Length', len(jsondataasbytes))
						f = urllib.request.urlopen(req, jsondataasbytes)	
					except urllib.error.HTTPError as e:
						self.writelog('1 The server couldn\'t fulfill the request. Error code: %s'%e.code)
					except urllib.error.URLError as e:
						self.writelog('1 We failed to reach a server. Reason: %s'%e.reason)

					else:
						#self.writelog(f.read())
						response = json.loads(f.read())
						# self.writelog(response)
						if response['success']:
							if response['success']== 1:
								sukses = 1
						f.close()
					if sukses == 1:
						self.dbQuery("update push_notification set push_notification_status = 1, push_notification_response = '%s' where push_notification_id = %s" %(json.dumps(response) ,dat[0]))
					else:
						self.dbQuery("update push_notification set push_notification_status = 2, push_notification_response = '%s' where push_notification_id = %s" %(json.dumps(response) ,dat[0]))
			sleep(1)
			if self.conn:
				self.conn.close()
		sleep(1)
		self.writelog("Closed...")
			             
id = IonicDaemon()
daemon_runner = runner.DaemonRunner(id)
daemon_runner.do_action()

