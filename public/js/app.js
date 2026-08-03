document.getElementById('taskForm').addEventListener('submit', async function (event) {
    event.preventDefault();

    const response = await fetch('/api/tasks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            title: document.getElementById('title').value,
            description: document.getElementById('description').value
        })
    });

    const data = await response.json();

    document.getElementById('result').textContent =
        JSON.stringify(data, null, 2);
});
