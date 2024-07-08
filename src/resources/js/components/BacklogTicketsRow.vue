<template>
    <tr v-if="filteredBacklogTickets.length">
        <td colspan="4" class="project-name">Backlog Tickets</td>
        <td v-for="date in dateRange" :key="date" :class="{
                'highlighted': isDateInBacklogRange(date),
                'is-weekend': isDateAWeekend(date),
                'is-today': isDateToday(date),
                ...priorityColourClass(date)
            }">
            <span v-if="isDateInBacklogRange(date)" class="tooltip">
                <img :src="getHighestPriorityBacklogTicket(date)?.icon" height="17" width="17" class="tooltip--img">
                <span class="tooltiptext">
                    <div v-for="ticket in getBacklogTicketsOnDate(date)" :key="ticket.id">
                        <a :href="`https://opialtd.atlassian.net/browse/${ticket.id}`" target="_blank">
                            {{ ticket.summary }}
                            ({{ ticket.priority }})
                        </a>
                    </div>
                </span>
            </span>
        </td>
    </tr>
</template>

<script>
import moment from 'moment';
import Highest from '../../images/priorities/highest.svg';
import High from '../../images/priorities/high.svg';
import Medium from '../../images/priorities/medium.svg';
import Low from '../../images/priorities/low.svg';
import Lowest from '../../images/priorities/lowest.svg';

export default {
    props: ['dateRange', 'backlogTickets', 'bankHolidays'],
    data() {
        return {
            priorityOrder: [
                {name: 'Highest', icon: Highest},
                {name: 'High', icon: High},
                {name: 'Medium', icon: Medium},
                {name: 'Low', icon: Low},
                {name: 'Lowest', icon: Lowest},
            ],
        };
    },
    computed: {
        filteredBacklogTickets() {
            return this.backlogTickets;
        },
    },
    methods: {
        isDateInBacklogRange(date) {
            const current = moment(date);
            return this.filteredBacklogTickets.some(ticket =>
                current.isBetween(moment(ticket.start_date), moment(ticket.end_date), 'days', '[]')
            );
        },
        getBacklogTicketsOnDate(date) {
            const current = moment(date);
            return this.filteredBacklogTickets.filter(ticket =>
                current.isBetween(moment(ticket.start_date), moment(ticket.end_date), 'days', '[]')
            );
        },
        getHighestPriorityBacklogTicket(date) {
            const tickets = this.getBacklogTicketsOnDate(date);
            const priorityOrder = this.priorityOrder.map(p => p.name);
            const ticket = tickets.sort((a, b) => priorityOrder.indexOf(a.priority) - priorityOrder.indexOf(b.priority))[0];
            return ticket ? this.priorityOrder.find(p => p.name === ticket.priority) : null;
        },
        priorityColourClass(date) {
            const priority = this.getHighestPriorityBacklogTicket(date);
            if (!priority) return {};
            return { [`highlighted--${priority.name.toLowerCase()}`]: true };
        },
        isDateAWeekend(date) {
            const current = moment(date);
            return [6, 0].includes(current.day()) || this.bankHolidays.includes(current.format('YYYY-MM-DD'));
        },
        isDateToday(date) {
            const current = moment();
            return current.format('YYYY-MM-DD') === date;
        },
    }
};
</script>

<style lang="scss" scoped>
.highlighted {
    background-color: #4CAF50;

    &.highlighted {
        position: relative;

        &--highest {
            background-color: #ff0000; // Change color as needed
        }

        &--high {
            background-color: #ff4500; // Change color as needed
        }

        &--medium {
            background-color: #ff8c00; // Change color as needed
        }

        &--low {
            background-color: #ffd700; // Change color as needed
        }

        &--lowest {
            background-color: #9acd32; // Change color as needed
        }
    }
}

.tooltip {
    display: inline-block;
    position: absolute;
    top: 0;
    left: 0;

    a {
        color: white;
        text-decoration: underline;
    }

    &--img {
        filter:
            drop-shadow( 1px  0px 0px white)
            drop-shadow(-1px  0px 0px white)
            drop-shadow( 0px  1px 0px white)
            drop-shadow( 0px -1px 0px white);
        position: relative;
        top: 1px;
        left: 1px;
    }

    .tooltiptext {
        visibility: hidden;
        width: 300px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 100%;
        left: 50%;
        margin-left: -150px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    &:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
}
</style>
