<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Manager</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8fafc;
    }
    .header {
      background: linear-gradient(90deg, #0d6efd 60%, #6ea8fe 100%);
      color: #fff;
      border-radius: 0.5rem;
      padding: 2rem 1rem 1.5rem 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      position: relative;
    }
    .add-task-btn {
      position: absolute;
      right: 2rem;
      top: 2rem;
      font-size: 1.1rem;
      box-shadow: 0 2px 8px rgba(13,110,253,0.08);
    }
    .table thead {
      background: #e9ecef;
    }
    .table-hover tbody tr:hover {
      background: #f1f3f5;
    }
    .modal-content {
      border-radius: 1rem;
    }
    .form-label {
      font-weight: 500;
    }
    @media (max-width: 600px) {
      .header { padding: 1.2rem 0.5rem 1rem 0.5rem; }
      .add-task-btn { right: 1rem; top: 1rem; }
      .table { font-size: 0.95rem; }
    }
  </style>
</head>
<body class="container my-5">

  <div class="header mb-4">
    <h1 class="mb-0">Task Manager</h1>
    <button class="btn btn-light add-task-btn" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="openCreateForm()">
      <span class="me-1">➕</span> Add Task
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle shadow-sm">
      <thead class="align-middle text-center">
        <tr><th>Title</th><th>Description</th><th>Status</th><th>Actions</th></tr>
      </thead>
      <tbody id="task-table-body"></tbody>
    </table>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="taskForm">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalTitle">Add/Edit Task</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="taskId">
            <div class="mb-3">
              <label for="title" class="form-label">Title</label>
              <input type="text" class="form-control" id="title" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description"></textarea>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="is_completed">
              <label class="form-check-label" for="is_completed">Completed</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('taskForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('taskId').value;
        const data = {
          title: document.getElementById('title').value,
          description: document.getElementById('description').value,
          is_completed: document.getElementById('is_completed').checked
        };

        if (id) {
          await axios.put(`${API}/${id}`, data);
        } else {
          await axios.post(API, data);
        }

        document.querySelector('#taskModal .btn-secondary').click();
        loadTasks();
      });
    });

    const API = '/api/tasks';

    async function loadTasks() {
      const res = await axios.get(API);
      const tbody = document.getElementById('task-table-body');
      tbody.innerHTML = '';
      res.data.data.forEach(task => {
        tbody.innerHTML += `
          <tr>
            <td>
              <span class="fw-semibold">${task.title}</span>
            </td>
            <td>${task.description || ''}</td>
            <td class="text-center">
              ${task.is_completed
                ? '<span class="badge bg-success">Completed</span>'
                : '<span class="badge bg-warning text-dark">Pending</span>'}
            </td>
            <td class="text-center">
              <button class="btn btn-sm btn-warning me-1" onclick="editTask(${task.id})"><i class="bi bi-pencil"></i> Edit</button>
              <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})"><i class="bi bi-trash"></i> Delete</button>
            </td>
          </tr>
        `;
      });
    }

    function openCreateForm() {
      document.getElementById('taskId').value = '';
      document.getElementById('title').value = '';
      document.getElementById('description').value = '';
      document.getElementById('is_completed').checked = false;
      document.getElementById('modalTitle').innerText = 'Add Task';
    }

    async function editTask(id) {
      const res = await axios.get(API);
      const task = res.data.data.find(t => t.id === id);
      document.getElementById('taskId').value = task.id;
      document.getElementById('title').value = task.title;
      document.getElementById('description').value = task.description || '';
      document.getElementById('is_completed').checked = task.is_completed;
      document.getElementById('modalTitle').innerText = 'Edit Task';
      new bootstrap.Modal(document.getElementById('taskModal')).show();
    }

    async function deleteTask(id) {
      if (confirm('Are you sure you want to delete?')) {
        await axios.delete(`${API}/${id}`);
        loadTasks();
      }
    }

    loadTasks();
  </script>
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
