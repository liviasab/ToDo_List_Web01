<template>
  <div class="task-list">
    <h1>Lista de tarefas</h1>
    <form @submit.prevent="addTask">
      <input
        type="text"
        v-model="newTaskTitle"
        placeholder="Nova Tarefa"
        required
        class="task-input"
      />
      <button type="submit" class="add-button">Adicionar</button>
    </form>
    <ul>
      <li class="task-item" v-for="task in tasks" :key="task.id">
        <input
          type="checkbox"
          :checked="task.completed == 1"
          @change="updateTaskCompletion(task)"
          class="task-checkbox"
        />
        <span :class="{ completed: task.completed == 1 }" class="task-title">{{ task.title }}</span>
        <button @click="deleteTask(task.id)" class="delete-button">Excluir</button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tasks: [],
      newTaskTitle: "",
    };
  },
  created() {
    this.fetchTasks();
  },
  methods: {
    fetchTasks() {
      fetch("http://localhost:8000/tasks")
        .then((response) => response.json())
        .then((data) => {
          this.tasks = data;
        })
        .catch((error) => {
          console.error("Erro ao buscar as tarefas:", error);
        });
    },
    addTask() {
      if (this.newTaskTitle) {
        const newTask = {
          title: this.newTaskTitle,
          completed: false,
        };
        fetch("http://localhost:8000/tasks", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(newTask),
        })
          .then((response) => response.json())
          .then((data) => {
            this.tasks.push(data);
            this.newTaskTitle = "";
          })
          .catch((error) => {
            console.error("Erro ao adicionar a tarefa:", error);
          });
      }
    },
    updateTaskCompletion(task) {
      task.completed = !task.completed;
      this.updateTask(task);
    },
    updateTask(task) {
      fetch(`http://localhost:8000/tasks/${task.id}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(task),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Erro ao atualizar a tarefa");
          }
          
          const updatedTaskIndex = this.tasks.findIndex((t) => t.id === task.id);
          if (updatedTaskIndex !== -1) {
            this.tasks.splice(updatedTaskIndex, 1, task);
          }
        })
        .catch((error) => {
          console.error("Erro ao atualizar a tarefa:", error);
        });
    },
    deleteTask(taskId) {
      fetch(`http://localhost:8000/tasks?id=${taskId}`, {
        method: "DELETE",
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Erro ao excluir a tarefa");
          }
          this.tasks = this.tasks.filter((task) => task.id !== taskId);
        })
        .catch((error) => {
          console.error("Erro ao excluir a tarefa:", error);
        });
    },
  },
};
</script>
<style>
.task-list {
  font-family: Arial, sans-serif;
  color: #000;
}

h1 {
  text-align: center;
  margin-bottom: 20px;
}

.task-input {
  padding: 10px;
  width: 200px;
  margin-right: 10px;
  border: 1px solid #000;
}

.add-button {
  padding: 10px;
  background-color: #000;
  color: #fff;
  border: none;
  cursor: pointer;
}

ul {
  list-style-type: none;
  padding: 0;
}

.task-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.task-checkbox {
  margin-right: 10px;
}

.task-title {
  flex-grow: 1;
}

.completed {
  text-decoration: line-through;
}

.delete-button {
  padding: 5px 10px;
  background-color: #000;
  color: #fff;
  border: none;
  cursor: pointer;
}
</style>


