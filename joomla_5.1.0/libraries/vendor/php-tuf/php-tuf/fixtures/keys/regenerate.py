# You should never need to run this, but this is the code that generated the
# fixed keys for fixture creation.

from tuf import repository_tool as rt


def write_and_import_keypair(filename):
    pathpriv = '{}_key'.format(filename)
    rt.generate_and_write_ed25519_keypair(pathpriv, password='pw')


for i in range(20):
    write_and_import_keypair(i)
