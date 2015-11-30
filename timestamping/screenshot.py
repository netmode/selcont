__author__ = 'coyiotis'

"""
Screen grabbing utility

Running in the background, it captures a screenshot when triggered by the following keys:
['Up', 'Down', 'Left', 'Right', 'PrintScreen', 'Space', 'PageUp', 'PageDown']
"""

import pyHook
import pythoncom
import time
import datetime
import wx
import os
import threading

lock = threading.Lock()

def get_timestamp():
    ts = time.time()
    st = datetime.datetime.fromtimestamp(ts).strftime('%Y_%m_%d_%H.%M.%S')
    return st

def get_formal_timestamp():
    ts = time.time()
    st = datetime.datetime.fromtimestamp(ts).strftime('%Y %m %d %H:%M:%S')
    return st

def get_date():
    ts = time.time()
    sd = datetime.datetime.fromtimestamp(ts).strftime('%Y_%m_%d')
    return sd


def OnKeyboardEvent(event):
    
    if event.Key in ['Up', 'Down', 'Left', 'Right', 'Snapshot', 'Space', 'Prior', 'Next']:
		
        t = threading.Thread(target=take_screen, args=(1,))
        t.start()
        # sys.exit()
		
    return True

def take_screen(i):
        lock.acquire()
		# sleep(.2)
        global screenshot_num
        timestamp = get_timestamp()

        picFolder = os.path.normpath(os.path.expanduser("~\Pictures\SeLCont"))

        s = wx.ScreenDC()
        w, h = s.Size.Get()
        b = wx.EmptyBitmap(w, h)
        m = wx.MemoryDCFromDC(s)
        m.SelectObject(b)
        m.Blit(0, 0, w, h, s, 0, 0)
        m.SelectObject(wx.NullBitmap)

        if not (os.path.exists(picFolder)):
            os.makedirs(picFolder)

        sd = get_date()
        filepath = os.path.join(picFolder, sd)
        if not (os.path.exists(filepath)):
            os.makedirs(filepath)

        fullpath = os.path.join(filepath, timestamp+".png")
        b.SaveFile(fullpath, wx.BITMAP_TYPE_PNG)
        print "ScreenShot " + str(screenshot_num) + " - " + get_formal_timestamp()
        try:
            screenshot_num += 1
        except:
            pass
        
        lock.release()
		
global screenshot_num
screenshot_num = 1
app = wx.App(False)  # Need to create an App instance before doing anything
hooks_manager = pyHook.HookManager()
hooks_manager.KeyUp = OnKeyboardEvent
hooks_manager.HookKeyboard()
pythoncom.PumpMessages()  # pythoncom module is used to capture the key messages.
