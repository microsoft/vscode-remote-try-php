# Delegation tree
#
#             Targets
#             /     \
#            a       f
#           / \
#          b   e
#         / \
#        c   d
#
# b is the only terminating delegation
#
# Roles should be evaluated in the order:
# Targets > a > b > c > d
#
# Roles e and d should not be evaluated.
from fixtures.builder import ConsistencyVariantFixtureBuilder


def build():
    ConsistencyVariantFixtureBuilder('TerminatingDelegation')\
        .publish(with_client=True)\
        .create_target('targets.txt')\
        .delegate('a', ['*.txt'])\
        .create_target('a.txt', signing_role='a')\
        .delegate('b', ['*.txt'], parent='a', terminating=True) \
        .create_target('b.txt', signing_role='b') \
        .delegate('c', ['*.txt'], parent='b') \
        .create_target('c.txt', signing_role='c') \
        .delegate('d', ['*.txt'], parent='b') \
        .create_target('d.txt', signing_role='d') \
        .delegate('e', ['*.txt'], parent='a') \
        .create_target('e.txt', signing_role='e') \
        .delegate('f', ['*.txt']) \
        .create_target('f.txt', signing_role='f') \
        .publish()
