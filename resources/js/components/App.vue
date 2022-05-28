<template>

    <div class="flex mt-5 flex-col items-center h-screen">
        <div class="col">
            <button @click="previousMonthClick" class="bg-gray-100 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">
                Prev
            </button>
            <b class="py-2 px-4"> {{ activeMonth }} </b>
            <button @click="nextMonthClick" class="bg-gray-100 hover:bg-gray-400 text-gray-800  py-2 px-4 rounded">
                Next
            </button>
        </div>
        <table class="table-fixed border border border-slate-400 text-center">
            <thead>
            <tr>
                <th class="border border-slate-300 px-4 py-2 ">Name Surname</th>
                <th v-for="date in dates" class="border border-slate-300 hover:bg-stone-100 px-4 py-2">
                    {{ date }}
                </th>
            </tr>
            </thead>
            <tbody v-for="(person, i) in employee" :key="person.id">
            <tr>
                <td class="border border-slate-300">{{ person.name }}</td>
                <td v-for="(hours, index) in person.workingDatesHours" :key="index" class="border border-slate-300 ">
                    <div>
                        <div class="" v-if="hoursEditingId == index && rowEditingId == person.id">
                            <input class="w-10 text-center" v-model="person.workingDatesHours[index]"
                                   :id="`hours-edit-${person.id}${index}`"
                                   @keydown.enter="updateHours(index, person, i)"/>
                        </div>
                        <div class="" v-else @click="setToEditing(index, person)">
                            {{ hours }}
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<script setup>

import {ref} from 'vue'

const employeeData = ref(window.data.employeeWorkingData);

const today = new Date();
const month = ref(today.getMonth() + 1);
const year = ref(today.getFullYear());

const dates = ref(getDays());
const activeMonth = ref(monthName(month.value));

const employee = ref(getEmployeeWorkingData());

const hoursEditingId = ref('');
const rowEditingId = ref('');

const setToEditing = (index, person) => {
    hoursEditingId.value = index;
    rowEditingId.value = person.id;

    setTimeout(() => {
        document.getElementById(`hours-edit-${person.id}${index}`).focus();
    }, 1);
}

const updateHours = (index, person, personIndex) => {

    const workedHours = Number(person.workingDatesHours[index]);
    const workingDate = index + 1;
    const dataToPass = {
        employeeId: person.id,
        date: workingDate,
        month: month.value,
        year: year.value,
        hours: workedHours
    };

    employeeData.value[personIndex]['working_hours'].push(
        {
            user_id: person.id, date: workingDate, month: month.value, year: year.value, hours: workedHours
        });

    axios.post('/employee/store', dataToPass).then(
        response => {
            console.log(response);

        }).catch(error => {
        console.log('Error here');
    });

    hoursEditingId.value = '';
    rowEditingId.value = '';
}

const previousMonthClick = () => {
    if (month.value === 1) {
        month.value = 12;
        year.value = year.value - 1;
        dates.value = getDays(year.value, month.value);
        activeMonth.value = monthName(month.value);
        employee.value = getEmployeeWorkingData(employeeData.value);
    } else {
        month.value = month.value - 1;
        dates.value = getDays(year.value, month.value);
        activeMonth.value = monthName(month.value);
        employee.value = getEmployeeWorkingData(employeeData.value);
    }
}

const nextMonthClick = () => {
    if (month.value === 12) {
        month.value = 1;
        year.value = year.value + 1;
        dates.value = getDays()
        activeMonth.value = monthName()
        employee.value = getEmployeeWorkingData(employeeData.value);
    } else {
        month.value = month.value + 1;
        dates.value = getDays()
        activeMonth.value = monthName()
        employee.value = getEmployeeWorkingData(employeeData.value);
    }
}

function getDays() {
    let monthIndex = month.value - 1;
    let date = new Date(year.value, monthIndex, 1);
    let result = [];
    while (date.getMonth() === monthIndex) {
        result.push(date.getDate());
        date.setDate(date.getDate() + 1);
    }
    return result;
}

function monthName() {
    let months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return months[month.value - 1];
}

function getEmployeeWorkingData() {
    let employeeWorkingData = [];
    employeeData.value.forEach(e => {
        let employeeName = e['name'];
        let employeeId = e['id'];

        const days = new Date(year.value, month.value, 0).getDate();
        let allDays = Array(days).fill('-');

        e['working_hours'].forEach(item => {
            if (month.value === parseInt(item['month']) && year.value === parseInt(item['year'])) {
                allDays[parseInt(item['date']) - 1] = item['hours'];
            }
        });

        employeeWorkingData.push({name: employeeName, workingDatesHours: allDays, id: employeeId});
    });
    return employeeWorkingData;
}
</script>
