const testBtn = document.getElementById("testBtn")
testBtn.addEventListener("click", async () => {
    let data = await testing()
    console.log(data)
})

async function testing() {
    const req = await fetch("http://localhost:8000/test")
    const data = await req.json()
    return data
}
