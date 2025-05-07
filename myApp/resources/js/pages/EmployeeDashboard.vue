
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import api from '@/services/api';
  
  const pending = ref<any[]>([]);
  const all = ref<any[]>([]);
  const grades = ['A', 'B', 'C', 'D', 'F'];
  const decisions = ['NOT YET TAKEN', 'STAYS AT COMPANY', 'MOVE TO DIFFERENT POSITION', 'LET GO'];
  //      api.get('/api/employees')
  const fetchData = async () => {
    const [p, a] = await Promise.all([,
      api.post('/employees?data=true'),
    ]);
    pending.value = p.data;
    all.value = a.data;
  };
  
  const saveUpdate = async (employee: any) => {
    await api.patch(`/employees/${employee.EmployeeID}`, {
      grade: employee.Grade,
      decision: employee.decision,
    });
    await fetchData();
  };
  
//   const undoDecision = async (id: number) => {
//     await api.patch(`/employees/${id}/remove-decision`);
//     await fetchData();
//   };

    const deleteDecision = async (id: number) => {
    await api.delete(`/employees/${id}`);
    await fetchData();
  };
  
  onMounted(fetchData);
  </script>
  <template>
    <div class="p-6">
      <h2 class="text-xl font-bold mb-4">CEO Dashboard</h2>
  
      <!-- Grid 1: Pending Employees -->
      <h3 class="text-lg font-semibold">Pending Employees</h3>
      <table class="w-full border mb-6">
        <thead>
          <tr>
            <th>Company</th>
            <th>Name</th>
            <th>Position</th>
            <th>Grade</th>
            <th>Decision</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in pending" :key="employee.EmployeeID">
            <td>{{ employee.Company }}</td>
            <td>{{ employee.name }}</td>
            <td>{{ employee.JobDescription }}</td>
            <td>
              <select v-model="employee.Grade" @change="saveUpdate(employee)">
                <option v-for="grade in grades" :key="grade" :value="grade">{{ grade }}</option>
              </select>
            </td>
            <td>
              <select v-model="employee.decision" @change="saveUpdate(employee)">
                <option v-for="option in decisions" :key="option" :value="option">{{ option }}</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
  
      <!-- Grid 2: All Employees -->
      <h3 class="text-lg font-semibold">All Employees</h3>
      <table class="w-full border">
        <thead>
          <tr>
            <th>Company</th>
            <th>Name</th>
            <th>Position</th>
            <th>Grade</th>
            <th>Decision</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="employee in all" :key="'all-' + employee.EmployeeID">
            <td>{{ employee.Company }}</td>
            <td>{{ employee.name }}</td>
            <td>{{ employee.JobDescription }}</td>
            <td>{{ employee.Grade }}</td>
            <td>{{ employee.decision }}</td>
            <td>
              <button @click="undoDecision(employee.EmployeeID)" class="text-red-600 font-bold">X</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  