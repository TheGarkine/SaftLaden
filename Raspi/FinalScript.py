import RPi.GPIO as GPIO
import MFRC522
import signal
import time
import requests
import json
import time
import lcddriver

import lcddriver
 
lcd = lcddriver.lcd()
lcd.lcd_clear()
 

GPIO.setwarnings(False)
"""
def end_read(signal,frame):
	global continue_reading
	print "Ctrl+C captured, ending read."
	continue_reading = False
	GPIO.cleanup()
"""
#signal.signal(signal.SIGINT, end_read)
MIFAREReader = MFRC522.MFRC522()

print "Press Ctrl-C to stop"
GPIO.setup(36, GPIO.IN, pull_up_down=GPIO.PUD_UP)


#speed per ml liquid pumped
speed_right = 6.1/200
speed_left = 8.0/200
pump_delay_right = 0.15
pump_delay_left = 0.28

linksGpio = 16
rechtsGpio = 18
gruenGpio = 38
rotGpio = 40

time.sleep(0.1)

GPIO.setup(linksGpio, GPIO.OUT)
GPIO.setup(rechtsGpio, GPIO.OUT)
GPIO.setup(gruenGpio, GPIO.OUT)
GPIO.setup(rotGpio, GPIO.OUT)
GPIO.output(rotGpio,0)
GPIO.output(gruenGpio,0)
GPIO.output(linksGpio,0)
GPIO.output(rechtsGpio,0)

lcd.lcd_display_string("WAIT FOR GLASS",1)
#busy waiting for input
while (True):
	
	(status, TagType) = MIFAREReader.MFRC522_Request(MIFAREReader.PICC_REQIDL)
	(status, uid) = MIFAREReader.MFRC522_Anticoll()
	if status == MIFAREReader.MI_OK:
		id = (((((uid[0] << 8) + uid[1]) << 8) +uid[2]) << 8) +uid[3]
		print "Scanned : " + str(id)
		lcd.lcd_display_string("RFID:"+ str(id),1)
		
		try:
			resp = requests.get("http://192.168.0.110/Saftladen/frontend/api.php?model=glass&id="+str(id))
		except:
			print ("ERROR IN HTTP REQUEST")
			exit()

		if(resp.status_code != 200):
			print("ERROR IN HTTP REQUEST: ", resp.status_code)
			exit()
		
		
		GPIO.output(gruenGpio,1);
		while True:
			input_state = GPIO.input(36)
			if input_state == False:
				GPIO.output(gruenGpio,0)
				print "Started inflow"
				break
				
		JSONResp = json.loads(resp.content)

		if(JSONResp['type'] == 'ok'):
			conf = JSONResp['data']
			print "loading config"
			Juice = {}
			Juice["volume"] = conf["volume"]
			Juice["left"] = conf["mixtures"][0]
			Juice["right"] = conf["mixtures"][1]
			print "volume : " + str(Juice["volume"])
			print "left : " + str(Juice["left"])
			print "right : " + str(Juice["right"])
		else:
			print "Failure on Data, type=" + JSONResp["type"]
			continue
		
		lcd.lcd_display_string("V:"+str(Juice["volume"]) +"L:" + str(Juice["left"]) + "R:" + str(Juice["right"]) ,2)
		
		time_left = Juice["left"]*speed_left*Juice["volume"] + pump_delay_left
		time_right = Juice["right"]*speed_right*Juice["volume"] + pump_delay_right
		
		if (time_left < time_right):
			GPIO.output(linksGpio, GPIO.HIGH)
			GPIO.output(rechtsGpio, GPIO.HIGH)
			time.sleep(time_left)
			GPIO.output(linksGpio, GPIO.LOW)
			time.sleep(time_right - time_left)
			GPIO.output(rechtsGpio, GPIO.LOW)
		else:
			GPIO.output(linksGpio, GPIO.HIGH)
			GPIO.output(rechtsGpio, GPIO.HIGH)
			time.sleep(time_right)
			GPIO.output(rechtsGpio, GPIO.LOW)
			time.sleep(time_left - time_right)
			GPIO.output(linksGpio, GPIO.LOW)
		
		
		lcd.lcd_clear()
		
		lcd.lcd_display_string("WAIT FOR END",1)
		time.sleep(2)
		x = 15
		while (x > 0):
			GPIO.output(rotGpio,1)
			time.sleep(0.3)
			GPIO.output(rotGpio,0)
			time.sleep(0.3)
			x = x-1
		
		lcd.lcd_display_string("WAIT FOR GLASS",1)
