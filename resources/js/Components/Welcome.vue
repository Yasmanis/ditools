<template>
    <div>
        <div class="controls">
            <button @click="startProcess" :disabled="isRunning" class="btn-start">
                Iniciar
            </button>
            <button @click="stopProcess" :disabled="!isRunning" class="btn-stop">
                Detener
            </button>
        </div>

        <div class="results">
            <h2>Resultados</h2>
            <div v-for="(log, index) in logs" :key="index" class="log-item">
                {{ log }}
            </div>
        </div>
    </div>
</template>

<script>
import {ref} from "vue";

export default {
    setup() {
        const isRunning = ref(false);
        const interval = ref(null);
        const logs = ref([]);

        const startProcess = () => {
            isRunning.value = true;
            interval.value = setInterval(async () => {
                try {
                    const response = await axios.get("/api/process");
                    const serverName = response.headers["server"];
                    const header = response.headers["responseof"];
                    const result = response.data;
                    console.log(response.headers)

                    logs.value.unshift(
                        `Header: ${header}, Consulta N°${result.query_no}, Duración: ${result.duration}ms, Resultado: ${result.status}, Mensaje: ${result.message}, Procesado en servidor: ${serverName}`
                    );
                } catch (error) {
                    const header = response.headers["responseof"];
                    const serverName = response.headers["server"];
                    logs.value.unshift(
                        `Header: ${header}, Server: ${serverName}, Error: ${error.message}, al realizar la consulta.`
                    );
                }
            }, 2000);
        };

        const stopProcess = () => {
            isRunning.value = false;
            clearInterval(interval.value);
            interval.value = null;
        };

        return {
            isRunning,
            logs,
            startProcess,
            stopProcess,
        };
    },
};
</script>


<style>
.controls {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 20px;
}

.btn-start,
.btn-stop {
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

.btn-start {
    background-color: #4caf50;
    color: white;
}

.btn-stop {
    background-color: #f44336;
    color: white;
}

.btn-start:disabled,
.btn-stop:disabled {
    background-color: #ddd;
    color: #999;
    cursor: not-allowed;
}

.btn-start:hover:not(:disabled),
.btn-stop:hover:not(:disabled) {
    transform: scale(1.05);
}

.results {
    margin: 30px auto;
    width: 90%;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 20px;
    overflow-y: auto;
}

.results h2 {
    margin-bottom: 15px;
    font-size: 20px;
    color: #333;
    text-align: center;
}

.log-item {
    padding: 10px;
    margin-bottom: 10px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    color: #555;
}

.log-item:nth-child(odd) {
    background-color: #f1f1f1;
}
</style>
