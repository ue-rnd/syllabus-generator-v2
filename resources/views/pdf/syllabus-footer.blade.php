<style>
    footer {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        font-family: 'Arial', sans-serif;
        font-size: 9px;
        color: #000000;
        width: 100%;
        text-align: center;
    }

    .left {
        text-align: left;
    }

    .right {
        text-align: right;
    }

</style>
<footer>
    <div class="left">
        <div>
            {{ $course->code ?? 'Course Code' }} - {{ $course->name ?? 'Course Name' }}
        </div>
        <div>
            University of the East - {{ $college->name ?? 'College Name' }}
        </div>
    </div>
    <div class="right">
        Page @pageNumber of @totalPages
    </div>
</footer>
