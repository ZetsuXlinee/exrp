import mysql from 'mysql2/promise';

export default async function handler(req, res) {
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET');

  const name = (req.query.name || '').trim();
  if (!name) return res.status(200).json({ exists: false });

  let conn;
  try {
    conn = await mysql.createConnection({
      host: process.env.DB_HOST || '116.212.73.51',
      port: 3306,
      user: process.env.DB_USER || 'u236_EHRIQGvk9J',
      password: process.env.DB_PASS || 'KpglPz.A6l2jB9PT23iOPG5c',
      database: process.env.DB_NAME || 's236_testing',
      connectTimeout: 5000
    });

    const queries = [
      "SELECT 1 FROM `playerucp` WHERE `ucp` =? LIMIT 1",
      "SELECT 1 FROM `playerucp` WHERE `username` =? LIMIT 1",
      "SELECT 1 FROM `playerucp` WHERE `name` =? LIMIT 1",
      "SELECT 1 FROM `player_characters` WHERE `Character` =? LIMIT 1",
      "SELECT 1 FROM `players` WHERE `Name` =? LIMIT 1"
    ];

    let found = false;
    let source = '';
    for (const sql of queries) {
      try {
        const [rows] = await conn.execute(sql, [name]);
        if (rows.length > 0) { found = true; source = sql; break; }
      } catch (e) { continue; }
    }

    return res.status(200).json({ exists: found, name, source });

  } catch (err) {
    return res.status(500).json({ exists: false, error: err.message });
  } finally {
    if (conn) await conn.end();
  }
}
