#include <stdio.h>
#include <unistd.h>

int main(void) {
    setuid(0);
    setgid(0);
    FILE *f = fopen("/flag.txt", "r");
    if (!f) {
        perror("Error");
        return 1;
    }
    char buf[256];
    while (fgets(buf, sizeof(buf), f))
        printf("%s", buf);
    fclose(f);
    return 0;
}
