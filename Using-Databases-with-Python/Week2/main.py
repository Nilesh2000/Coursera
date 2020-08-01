import sqlite3

conn = sqlite3.connect("emaildb.sqlite")
cur = conn.cursor()

cur.execute("DROP TABLE IF EXISTS counts")

cur.execute("CREATE TABLE Counts(org TEXT, count INTEGER)")
fname = "mbox.txt"
fh = open(fname)
for line in fh:
    if not line.startswith("From: "):
        continue

    words = line.split()
    email = words[1]
    email_split = email.split("@")
    org = email_split[1]
    cur.execute("SELECT count FROM counts WHERE org = ? ", (org,))
    row = cur.fetchone()
    if row is None:
        cur.execute(
            "INSERT INTO counts (org, count) VALUES (?, 1)", (org,),
        )
    else:
        cur.execute(
            "UPDATE counts SET count = count + 1 WHERE org = ?", (org,),
        )
    conn.commit()
