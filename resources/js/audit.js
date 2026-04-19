const DB_NAME  = 'PersonManagerAudit'
const STORE    = 'logs'
const DB_VER   = 1

function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VER)
        req.onupgradeneeded = e => {
            const db    = e.target.result
            const store = db.createObjectStore(STORE, { keyPath: 'sn', autoIncrement: true })
            store.createIndex('action',   'action',   { unique: false })
            store.createIndex('datetime', 'datetime', { unique: false })
        }
        req.onsuccess = e => resolve(e.target.result)
        req.onerror   = e => reject(e.target.error)
    })
}

export async function logAudit(action, detail) {
    const db = await openDB()
    return new Promise((resolve, reject) => {
        const tx  = db.transaction(STORE, 'readwrite')
        const req = tx.objectStore(STORE).add({
            action,
            detail,
            datetime: new Date().toLocaleString('en-GB', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            })
        })
        req.onsuccess = () => resolve()
        req.onerror   = e => reject(e.target.error)
    })
}

export async function getAuditLogs() {
    const db = await openDB()
    return new Promise((resolve, reject) => {
        const tx  = db.transaction(STORE, 'readonly')
        const req = tx.objectStore(STORE).getAll()
        req.onsuccess = e => resolve([...e.target.result].reverse())
        req.onerror   = e => reject(e.target.error)
    })
}