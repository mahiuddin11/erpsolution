(function () {

    const words = {
        0: '',
        1: 'One',
        2: 'Two',
        3: 'Three',
        4: 'Four',
        5: 'Five',
        6: 'Six',
        7: 'Seven',
        8: 'Eight',
        9: 'Nine',
        10: 'Ten',
        11: 'Eleven',
        12: 'Twelve',
        13: 'Thirteen',
        14: 'Fourteen',
        15: 'Fifteen',
        16: 'Sixteen',
        17: 'Seventeen',
        18: 'Eighteen',
        19: 'Nineteen',
        20: 'Twenty',
        30: 'Thirty',
        40: 'Forty',
        50: 'Fifty',
        60: 'Sixty',
        70: 'Seventy',
        80: 'Eighty',
        90: 'Ninety'
    };

    function convertBelowThousand(num) {

        num = parseInt(num);

        if (num === 0) return '';

        if (num < 20)
            return words[num];

        if (num < 100)
            return words[Math.floor(num / 10) * 10] +
                (num % 10 ? ' ' + words[num % 10] : '');

        return words[Math.floor(num / 100)] +
            ' Hundred' +
            (num % 100 ? ' ' + convertBelowThousand(num % 100) : '');
    }

    function numberToWords(number) {

        number = parseFloat(number);

        if (isNaN(number) || number <= 0)
            return '';

        let taka = Math.floor(number);
        let paisa = Math.round((number - taka) * 100);

        const units = [
            { value: 1000000000000, name: 'Lakh Crore' },
            { value: 10000000000, name: 'Thousand Crore' },
            { value: 1000000000, name: 'Hundred Crore' },
            { value: 10000000, name: 'Crore' },
            { value: 100000, name: 'Lakh' },
            { value: 1000, name: 'Thousand' },
            { value: 100, name: 'Hundred' }
        ];

        let result = '';

        units.forEach(unit => {

            if (taka >= unit.value) {

                const q = Math.floor(taka / unit.value);

                result += convertBelowThousand(q) + ' ' + unit.name + ' ';

                taka %= unit.value;

            }

        });

        if (taka > 0) {

            result += convertBelowThousand(taka);

        }

        result = result.trim() + ' Taka';

        if (paisa > 0) {

            result += ' And ' + convertBelowThousand(paisa) + ' Paisa';

        }

        return result + ' Only';

    }

    function initNumberWordsInputs() {

        document.querySelectorAll('[data-number-words]').forEach(function (input) {

            const target = document.getElementById(input.dataset.numberWords);

            if (!target) return;

            const update = () => {

                target.textContent = numberToWords(input.value);

            };

            input.addEventListener('input', update);

            update();

        });

    }

    document.addEventListener('DOMContentLoaded', initNumberWordsInputs);

    window.numberToWords = numberToWords;
    window.initNumberWordsInputs = initNumberWordsInputs;

})();