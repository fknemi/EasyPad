const API = "http://localhost:8000/api";
const FRONTEND_BASE = "http://localhost:5173";

const title = document.getElementById("title");
const content = document.getElementById("content");
const status = document.getElementById("status");
const saveBtn = document.getElementById("save");
const shareBtn = document.getElementById("share");

const modal = document.getElementById("share-modal");
const shareInput = document.getElementById("share-link");
const closeShare = document.getElementById("close-share");
const copyLink = document.getElementById("copy-link");

const createdDateEl = document.getElementById("created-date");
const updatedDateEl = document.getElementById("updated-date");

let noteId = null;
let shareToken = null;
let isShared = false;
let canEdit = true;

/* -------- Helpers -------- */
function setStatus(msg) {
  status.textContent = msg;
  setTimeout(() => {
    status.textContent = "";
  }, 2000);
}

function getQueryParam(name) {
  return new URLSearchParams(window.location.search).get(name);
}

function formatDateTime(ts) {
  const d = new Date(ts);
  const pad = (n) => n.toString().padStart(2, "0");

  const day = pad(d.getDate());
  const month = pad(d.getMonth() + 1);
  const year = d.getFullYear();
  const hours = pad(d.getHours());
  const minutes = pad(d.getMinutes());
  const seconds = pad(d.getSeconds());

  return `${day}-${month}-${year} ${hours}:${minutes}:${seconds}`;
}

/* -------- Load Note -------- */
async function loadNote() {
  const qNote = getQueryParam("noteId");
  const qShare = getQueryParam("share");

  try {
    if (qNote) {
      noteId = qNote;
      const res = await fetch(`${API}/notes/${noteId}`);
      const data = await res.json();
      if (!data.success) return alert("Note not found");

      title.value = data.note.title;
      content.value = data.note.content;
      createdDateEl.textContent = formatDateTime(data.note.created_at);
      updatedDateEl.textContent = formatDateTime(data.note.updated_at);
    } else if (qShare) {
      shareToken = qShare;
      isShared = true;

      const res = await fetch(`${API}/share/${shareToken}`);
      const data = await res.json();
      if (!data.success) return alert("Shared note not found");

      title.value = data.note.title;
      content.value = data.note.content;
      createdDateEl.textContent = formatDateTime(data.note.created_at);
      updatedDateEl.textContent = formatDateTime(data.note.updated_at);

      canEdit = data.note.can_edit;
      if (!canEdit) saveBtn.disabled = true;
    }
  } catch (err) {
    console.error("Error loading note:", err);
    alert("Failed to load note");
  }
}

/* -------- Save Note -------- */
async function saveNote() {
  if (isShared && !canEdit) return alert("No edit permission");
  setStatus("Saving…");

  try {
    if (isShared) {
      const res = await fetch(`${API}/share/${shareToken}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          title: title.value,
          content: content.value,
        }),
      });
      const data = await res.json();
      if (!data.success) return setStatus("Save failed");

      updatedDateEl.textContent = formatDateTime(data.note.updated_at);
      return setStatus("Shared note updated ✔");
    }

    if (!noteId) {
      const res = await fetch(`${API}/notes`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title: title.value, content: content.value }),
      });
      const data = await res.json();
      if (!data.success) return setStatus("Save failed");

      noteId = data.note.id;
      createdDateEl.textContent = formatDateTime(data.note.created_at);
      updatedDateEl.textContent = formatDateTime(data.note.updated_at);
      setStatus("Saved ✔");
      history.replaceState(null, "", `?noteId=${noteId}`);
    } else {
      const res = await fetch(`${API}/notes/${noteId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ title: title.value, content: content.value }),
      });
      const data = await res.json();
      if (!data.success) return setStatus("Save failed");

      updatedDateEl.textContent = formatDateTime(data.note.updated_at);
      setStatus("Updated ✔");
    }
  } catch (err) {
    console.error("Error saving note:", err);
    setStatus("Save failed");
  }
}

/* -------- Share Note -------- */
shareBtn.onclick = async () => {
  if (isShared) return alert("Already shared");
  if (!noteId) return alert("Save the note first");

  try {
    const res = await fetch(`${API}/share`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ note_id: noteId, can_edit: true }),
    });
    const data = await res.json();
    if (!data.success) return alert("Share failed");

    shareToken = data.share_token;
    shareInput.value = `${FRONTEND_BASE}?share=${shareToken}`;
    modal.classList.remove("hidden");
  } catch (err) {
    console.error("Error sharing note:", err);
    alert("Share failed");
  }
};

/* -------- Modal -------- */
closeShare.onclick = () => modal.classList.add("hidden");
copyLink.onclick = () => {
  shareInput.select();
  document.execCommand("copy");
  copyLink.textContent = "Copied!";
  setTimeout(() => (copyLink.textContent = "Copy"), 1500);
};

/* -------- Init -------- */
saveBtn.onclick = saveNote;
window.onload = loadNote;
